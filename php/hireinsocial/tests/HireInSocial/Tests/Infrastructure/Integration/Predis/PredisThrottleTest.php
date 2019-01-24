<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Infrastructure\Integration\Predis;

use function HireInSocial\bootstrap;
use HireInSocial\Config;
use HireInSocial\Infrastructure\Predis\PredisThrottle;
use HireInSocial\Tests\Application\MotherObject\Facebook\CalendarMother;
use PHPUnit\Framework\TestCase;
use Predis\Client;

final class PredisThrottleTest extends TestCase
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Client
     */
    private $predis;

    public function setUp()
    {
        $this->config = bootstrap(ROOT_DIR);
        $this->predis = new \Predis\Client($this->config->getString(Config::REDIS_DSN));
        $this->predis->flushall();
    }

    protected function tearDown()
    {
        $this->predis->flushall();
    }

    public function test_throttling()
    {
        $throttle = new PredisThrottle(
            new \Predis\Client($this->config->getString(Config::REDIS_DSN)),
            CalendarMother::utc(),
            new \DateInterval('PT05M'),
            'throttle-'
        );

        $throttleId = 'id';
        $this->assertFalse($throttle->isThrottled($throttleId));
        $throttle->throttle($throttleId);
        $this->assertTrue($throttle->isThrottled($throttleId));
        $this->assertFalse($throttle->isThrottled('other-id'));
    }
}

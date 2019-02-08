<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Integration;

use HireInSocial\Tests\Application\Context\DatabaseContext;
use HireInSocial\Config;
use HireInSocial\Tests\Application\Context\ThrottleContext;
use PHPUnit\Framework\TestCase;
use function HireInSocial\bootstrap;
use function HireInSocial\dbal;
use function HireInSocial\system;
use Predis\Client;

class HireInSocialTestCase extends TestCase
{
    /**
     * @var \HireInSocial\Tests\Application\Context\SystemContext
     */
    protected $systemContext;

    /**
     * @var DatabaseContext
     */
    protected $databaseContext;

    /**
     * @var ThrottleContext
     */
    protected $throttleContext;

    public function setUp()
    {
        $config = bootstrap(ROOT_DIR);

        if ($config->getString(Config::ENV) !== 'test') {
            $this->fail(sprintf('Expected environment "test" but got "%s"', $config->getString(Config::ENV)));
        }

        $this->systemContext = new \HireInSocial\Tests\Application\Context\SystemContext(system($config));
        $this->databaseContext = new DatabaseContext(dbal($config));
        $this->throttleContext = new ThrottleContext(new Client($config->getString(Config::REDIS_DSN)));

        $this->databaseContext->purgeDatabase();
        $this->throttleContext->purgeThrottles();
    }
}

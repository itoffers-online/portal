<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Integration;

use HireInSocial\Tests\Application\Context\DatabaseContext;
use HireInSocial\Config;
use PHPUnit\Framework\TestCase;
use function HireInSocial\bootstrap;
use function HireInSocial\dbal;
use function HireInSocial\system;

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

    public function setUp()
    {
        $config = bootstrap(ROOT_DIR);

        if ($config->getString(Config::ENV) !== 'test') {
            $this->fail(sprintf('Expected environment "test" but got "%s"', $config->getString(Config::ENV)));
        }

        $this->systemContext = new \HireInSocial\Tests\Application\Context\SystemContext(system($config));
        $this->databaseContext = new DatabaseContext(dbal($config));

        $this->databaseContext->purgeDatabase();
    }
}

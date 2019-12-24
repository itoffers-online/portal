<?php

declare(strict_types=1);

/*
 * This file is part of the Hire in Social project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Functional;

use function App\symfony;
use App\SymfonyKernel;
use HireInSocial\Application\Config;
use HireInSocial\Application\System;
use function HireInSocial\Infrastructure\bootstrap;
use function HireInSocial\Infrastructure\dbal;
use function HireInSocial\Infrastructure\system;
use HireInSocial\Tests\Application\Context\DatabaseContext;
use HireInSocial\Tests\Application\Context\SystemContext;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SymfonyKernelTestCase extends KernelTestCase
{
    /**
     * @var Config
     */
    protected static $config;
    /**
     * @var System
     */
    protected static $system;

    /**
     * @var SystemContext
     */
    protected $systemContext;

    /**
     * @var DatabaseContext
     */
    protected $databaseContext;

    protected static function getKernelClass()
    {
        return SymfonyKernel::class;
    }

    protected static function createKernel(array $options = [])
    {
        return symfony(static::config(), static::system());
    }

    protected static function config() : Config
    {
        if (!static::$config) {
            static::$config = bootstrap(ROOT_DIR);
        }

        if (static::$config->getString(Config::ENV) !== 'test') {
            throw new \RuntimeException(sprintf('Expected environment "test" but got "%s"', static::$config->getString(Config::ENV)));
        }

        return static::$config;
    }

    protected static function system() : System
    {
        if (!static::$system) {
            static::$system = system(static::config());
        }

        return static::$system;
    }

    public function setUp() : void
    {
        $config = static::config();

        $this->systemContext = new SystemContext(static::system());
        $this->databaseContext = new DatabaseContext(dbal($config));

        $this->databaseContext->purgeDatabase();
    }
}

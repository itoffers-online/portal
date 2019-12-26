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
use function HireInSocial\Infrastructure\offersFacade;
use HireInSocial\Offers;
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
     * @var Offers
     */
    protected static $offersFacade;

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
        return symfony(static::config(), static::offersFacade());
    }

    protected static function config() : Config
    {
        if (null === static::$config) {
            static::$config = bootstrap(ROOT_DIR);
        }

        if (static::$config->getString(Config::ENV) !== 'test') {
            throw new \RuntimeException(sprintf('Expected environment "test" but got "%s"', static::$config->getString(Config::ENV)));
        }

        return static::$config;
    }

    protected static function offersFacade() : Offers
    {
        if (null === static::$offersFacade) {
            static::$offersFacade = offersFacade(static::config());
        }

        return static::$offersFacade;
    }

    public function setUp() : void
    {
        $config = static::config();

        $this->systemContext = new SystemContext(static::offersFacade());
        $this->databaseContext = new DatabaseContext(dbal($config));

        $this->databaseContext->purgeDatabase();
    }
}

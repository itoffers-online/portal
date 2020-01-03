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

namespace App\Tests\Functional\Web;

use function App\symfony;
use App\SymfonyKernel;
use HireInSocial\Offers\Application\Config;
use function HireInSocial\Offers\Infrastructure\bootstrap;
use function HireInSocial\Offers\Infrastructure\dbal;
use function HireInSocial\Offers\Infrastructure\offersFacade;
use HireInSocial\Offers\Offers;
use HireInSocial\Tests\Offers\Application\Context\DatabaseContext;
use HireInSocial\Tests\Offers\Application\Context\OffersContext;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class SymfonyKernelTestCase extends KernelTestCase
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
     * @var OffersContext
     */
    protected $offersContext;

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

        $this->offersContext = new OffersContext(static::offersFacade());
        $this->databaseContext = new DatabaseContext(dbal($config));

        $this->databaseContext->purgeDatabase();
    }
}

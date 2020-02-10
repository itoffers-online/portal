<?php

declare(strict_types=1);

/*
 * This file is part of the itoffers.online project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Functional\Web;

use function App\symfony;
use App\SymfonyKernel;
use ITOffers\Config;
use ITOffers\ITOffersOnline;
use function ITOffers\Offers\Infrastructure\bootstrap;
use ITOffers\Offers\Offers;
use ITOffers\Tests\Offers\Application\Context\DatabaseContext;
use ITOffers\Tests\Offers\Application\Context\OffersContext;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class SymfonyKernelTestCase extends KernelTestCase
{
    /**
     * @var ITOffersOnline
     */
    protected static $hireInSocial;

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
        return symfony(bootstrap(ROOT_DIR));
    }

    protected static function hireInSocial() : ITOffersOnline
    {
        if (null === static::$hireInSocial) {
            static::$hireInSocial = static::$kernel->getContainer()->get(ITOffersOnline::class);
        }

        if (static::$hireInSocial->config()->getString(Config::ENV) !== 'test') {
            throw new \RuntimeException(sprintf('Expected environment "test" but got "%s"', static::$hireInSocial->config()->getString(Config::ENV)));
        }

        return static::$hireInSocial;
    }

    protected static function offersFacade() : Offers
    {
        return static::hireInSocial()->offers();
    }

    public function setUp() : void
    {
        static::bootKernel();

        $this->offersContext = new OffersContext(static::offersFacade());
        $this->databaseContext = new DatabaseContext(static::hireInSocial()->dbal());

        $this->databaseContext->purgeDatabase();
    }
}

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

use Aeon\Calendar\Gregorian\DateTime;
use Aeon\Calendar\Gregorian\GregorianCalendarStub;
use function App\initializeSymfony;
use App\SymfonyKernel;
use ITOffers\Config;
use ITOffers\ITOffersOnline;
use function ITOffers\Offers\Infrastructure\bootstrap;
use ITOffers\Offers\Offers;
use ITOffers\Tests\Offers\Application\Context\DatabaseContext;
use ITOffers\Tests\Offers\Application\Context\OffersContext;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class SymfonyKernelTestCase extends KernelTestCase
{
    protected static ?ITOffersOnline $itoffers = null;

    protected OffersContext $offersContext;

    protected DatabaseContext $databaseContext;

    protected static function getKernelClass()
    {
        return SymfonyKernel::class;
    }

    protected static function createKernel(array $options = [])
    {
        return initializeSymfony(bootstrap(ROOT_DIR));
    }

    public function setUp() : void
    {
        static::bootKernel();

        $this->offersContext = new OffersContext(static::offersFacade());
        $this->databaseContext = new DatabaseContext(static::itoffers()->dbal());

        $this->databaseContext->purgeDatabase();
        /** @var GregorianCalendarStub $calendar */
        $calendar = static::itoffers()->calendar();

        $calendar->setNow(DateTime::fromString('now'));
    }

    protected static function itoffers() : ITOffersOnline
    {
        if (null === static::$itoffers) {
            static::$itoffers = static::$kernel->getContainer()->get(ITOffersOnline::class);
        }

        if (static::$itoffers->config()->getString(Config::ENV) !== 'test') {
            throw new RuntimeException(sprintf('Expected environment "test" but got "%s"', static::$itoffers->config()->getString(Config::ENV)));
        }

        return static::$itoffers;
    }

    protected static function offersFacade() : Offers
    {
        return static::itoffers()->offers();
    }

    public function config() : Config
    {
        return static::itoffers()->config();
    }

    public function setCurrentTime(DateTime $currentTime) : void
    {
        /** @var GregorianCalendarStub $calendar */
        $calendar = static::itoffers()->calendar();

        $calendar->setNow($currentTime);
    }
}

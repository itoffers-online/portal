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

namespace App\Tests\Offers\Functional\Console\Offer;

use Aeon\Calendar\Gregorian\DateTime;
use App\Offers\Command\Offer\AutoRenewOffers;
use App\Tests\Functional\Console\ConsoleTestCase;
use ITOffers\Offers\Application\Command\Offer\AssignAutoRenew;
use ITOffers\Offers\Application\Query\Offer\Model\Offer;
use ITOffers\Offers\Application\Query\Offer\OfferFilter;
use ITOffers\Tests\Offers\Application\MotherObject\Command\Offer\PostOfferMother;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class AutoRenewOffersTest extends ConsoleTestCase
{
    public function test_auto_renewing_offers_without_anything_to_renew() : void
    {
        $command = new AutoRenewOffers(self::offersFacade());
        $application = new Application('test');
        $application->add($command);

        $commandTester = new CommandTester($application->find(AutoRenewOffers::NAME));
        $commandTester->execute(
            [
                'command'  => AutoRenewOffers::NAME,
            ],
            [
                'interactive' => false,
            ]
        );

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertStringContainsString(
            'There are no offers applicable for auto renew.',
            $commandTester->getDisplay(true)
        );
    }

    public function test_auto_renewing_offers() : void
    {
        $command = new AutoRenewOffers(self::offersFacade());
        $application = new Application('test');
        $application->add($command);


        $this->setCurrentTime(DateTime::fromString('-30 days'));

        $offer = $this->createOffer();
        $this->offersContext->module()->handle(new AssignAutoRenew(
            $offer->userId()->toString(),
            $offer->id()->toString()
        ));

        $this->setCurrentTime($currentDate = DateTime::fromString('now'));

        $commandTester = new CommandTester($application->find(AutoRenewOffers::NAME));
        $commandTester->execute(
            [
                'command'  => AutoRenewOffers::NAME,
            ],
            [
                'interactive' => false,
            ]
        );

        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('Successfully renewed 1 offers!', $commandTester->getDisplay(true));

        $this->assertEquals(
            $currentDate,
            $this->offersContext->module()->offerQuery()->findById($offer->id()->toString())->createdAt()
        );
        $this->assertSame(
            0,
            $this->offersContext->module()->offerAutoRenewQuery()->countUnassignedNotExpired($offer->userId()->toString())
        );
    }

    public function createOffer() : Offer
    {
        $user = $this->offersContext->createUser();
        $this->offersContext->addOfferAutRenewOffer($user, 1);
        $this->offersContext->createSpecialization('spec');
        $this->offersContext->module()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $user->id(), 'spec'));

        return $this->offersFacade()->offerQuery()->findAll(OfferFilter::allFor('spec'))->first();
    }
}

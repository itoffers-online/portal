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

use App\Offers\Command\Offer\RemoveOffer;
use App\Tests\Functional\Console\ConsoleTestCase;
use ITOffers\Offers\Application\Query\Offer\Model\Offer;
use ITOffers\Offers\Application\Query\Offer\OfferFilter;
use ITOffers\Tests\Offers\Application\MotherObject\Command\Offer\PostOfferMother;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class OfferRemoveTest extends ConsoleTestCase
{
    public function test_removing_offer() : void
    {
        $offer = $this->createOffer();

        $command = new RemoveOffer(self::offersFacade());
        $application = new Application('test');
        $application->add($command);

        $commandTester = new CommandTester($application->find(RemoveOffer::NAME));
        $commandTester->execute(
            [
                'command'  => RemoveOffer::NAME,
                'slug' => $offer->slug(),
            ],
            [
                'interactive' => false,
            ]
        );

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertNull($this->offersContext->module()->offerQuery()->findById($offer->id()->toString()));
    }

    public function createOffer() : Offer
    {
        $user = $this->offersContext->createUser();
        $this->offersContext->createSpecialization('spec');
        $this->offersContext->module()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $user->id(), 'spec'));

        return $this->offersFacade()->offerQuery()->findAll(OfferFilter::allFor('spec'))->first();
    }
}

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

namespace App\Tests\Offers\Functional\Console\Offer;

use App\Offers\Command\Offer\RemoveOffer;
use App\Tests\Functional\Console\ConsoleTestCase;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer;
use HireInSocial\Offers\Application\Query\Offer\OfferFilter;
use HireInSocial\Tests\Offers\Application\MotherObject\Command\Offer\PostOfferMother;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class OfferRemoveTest extends ConsoleTestCase
{
    public function test_removing_offer() : void
    {
        $offer = $this->createOffer();

        $command = new RemoveOffer(self::$offersFacade);
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
    }

    public function createOffer() : Offer
    {
        $user = $this->systemContext->createUser();
        $this->systemContext->createSpecialization('spec');
        $this->systemContext->offersFacade()->handle(PostOfferMother::random($user->id(), 'spec'));

        $offer = $this->offersFacade()->offerQuery()->findAll(OfferFilter::allFor('spec'))->first();

        return $offer;
    }
}
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

namespace App\Tests\Offers\Functional\Console\User;

use App\Offers\Command\User\BlockUser;
use App\Tests\Functional\Console\ConsoleTestCase;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer;
use HireInSocial\Offers\Application\Query\Offer\OfferFilter;
use HireInSocial\Tests\Offers\Application\MotherObject\Command\Offer\PostOfferMother;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class BlockUserTest extends ConsoleTestCase
{
    public function test_removing_offer() : void
    {
        $offer = $this->createOffer();

        $command = new BlockUser(self::$offersFacade);
        $application = new Application('test');
        $application->add($command);

        $commandTester = new CommandTester($application->find(BlockUser::NAME));
        $commandTester->execute(
            [
                'command'  => BlockUser::NAME,
                'slug' => $offer->slug(),
            ],
            [
                'interactive' => false,
            ]
        );

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertTrue($this->offersContext->offersFacade()->userQuery()->findById($offer->userId()->toString())->isBlocked());
    }

    public function createOffer() : Offer
    {
        $user = $this->offersContext->createUser();
        $this->offersContext->createSpecialization('spec');
        $this->offersContext->offersFacade()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $user->id(), 'spec'));

        return $this->offersFacade()->offerQuery()->findAll(OfferFilter::allFor('spec'))->first();
    }
}

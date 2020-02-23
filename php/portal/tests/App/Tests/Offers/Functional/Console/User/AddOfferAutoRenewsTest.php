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

namespace App\Tests\Offers\Functional\Console\User;

use App\Offers\Command\User\AddOfferAutoRenews;
use App\Tests\Functional\Console\ConsoleTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class AddOfferAutoRenewsTest extends ConsoleTestCase
{
    public function test_removing_offer() : void
    {
        $user = $this->offersContext->createUser();

        $command = new AddOfferAutoRenews(self::offersFacade());
        $application = new Application('test');
        $application->add($command);

        $commandTester = new CommandTester($application->find(AddOfferAutoRenews::NAME));
        $commandTester->execute(
            [
                'command'  => AddOfferAutoRenews::NAME,
                'email' => $user->email(),
                'count' => 5,
                'expiresInDays' => 30,
            ],
            [
                'interactive' => false,
            ]
        );

        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertSame(5, $this->offersContext->module()->offerAutoRenewQuery()->countUnassignedNotExpired($user->id()));
    }
}

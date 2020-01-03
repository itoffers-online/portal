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

namespace HireInSocial\Tests\Offers\Application\Integration\Command\User;

use HireInSocial\Offers\Application\Command\User\AddExtraOffers;
use HireInSocial\Tests\Offers\Application\Integration\HireInSocialTestCase;

final class AddExtraOffersTest extends HireInSocialTestCase
{
    public function test_adding_extra_offers() : void
    {
        $user = $this->systemContext->createUser();

        $this->systemContext->offersFacade()->handle(
            new AddExtraOffers(
                $user->id(),
                $count = 5,
                $expiresInDays = 1
            )
        );

        $this->assertSame(5, $this->systemContext->offersFacade()->extraOffersQuery()->countNotExpired($user->id()));
    }
}

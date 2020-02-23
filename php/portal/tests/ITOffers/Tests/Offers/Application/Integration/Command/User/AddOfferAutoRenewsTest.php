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

namespace ITOffers\Tests\Offers\Application\Integration\Command\User;

use ITOffers\Offers\Application\Command\User\AddOfferAutoRenews;
use ITOffers\Tests\Offers\Application\Integration\OffersTestCase;

final class AddOfferAutoRenewsTest extends OffersTestCase
{
    public function test_adding_offer_autor_renews() : void
    {
        $user = $this->offers->createUser();

        $this->offers->module()->handle(
            new AddOfferAutoRenews(
                $user->id(),
                $count = 5,
                $expiresInDays = 1
            )
        );

        $this->assertSame(5, $this->offers->module()->offerAutoRenewQuery()->countUnassignedNotExpired($user->id()));
    }
}

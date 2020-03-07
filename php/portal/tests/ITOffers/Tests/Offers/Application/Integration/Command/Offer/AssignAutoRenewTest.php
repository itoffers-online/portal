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

namespace ITOffers\Tests\Offers\Application\Integration\Command\Offer;

use ITOffers\Offers\Application\Command\Offer\AssignAutoRenew;
use ITOffers\Tests\Offers\Application\Integration\OffersTestCase;

final class AssignAutoRenewTest extends OffersTestCase
{
    public function test_assigning_auto_renew_to_the_offer() : void
    {
        $user = $this->offers->createUser();
        $this->offers->createSpecialization($specializationSlug = 'spec');
        $this->offers->addOfferAutRenewOffer($user, 1);
        $offer = $this->offers->createOffer($user->id(), $specializationSlug);

        $this->offers->module()->handle(new AssignAutoRenew(
            $user->id(),
            $offer->id()->toString(),
        ));

        $this->assertSame(1, $this->offers->module()->offerAutoRenewQuery()->countRenewsLeft($offer->id()->toString()));
    }
}

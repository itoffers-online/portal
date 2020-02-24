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

use ITOffers\Offers\Application\Command\Offer\RemoveOffer;
use ITOffers\Offers\Application\Query\Offer\OfferFilter;
use ITOffers\Tests\Offers\Application\Integration\OffersTestCase;
use ITOffers\Tests\Offers\Application\MotherObject\Command\Offer\PostOfferMother;
use Ramsey\Uuid\Uuid;

final class RemoveOfferTest extends OffersTestCase
{
    public function test_removing_offer() : void
    {
        $user = $this->offers->createUser();
        $this->offers->createSpecialization($specialization = 'spec');
        $this->offers->module()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $user->id(), $specialization));

        $offerQuery = $this->offers->module()->offerQuery();

        $offer = $offerQuery->findAll(OfferFilter::allFor($specialization))->first();

        $this->offers->module()->handle(new RemoveOffer($offer->id()->toString(), $user->id()));

        $this->assertEquals(0, $offerQuery->total());
        $this->assertEquals(0, $offerQuery->findAll(OfferFilter::all())->count());
        $this->assertEquals(0, $offerQuery->findAll(OfferFilter::allFor($specialization))->count());
        $this->assertNull($offerQuery->findById($offer->id()->toString()));
        $this->assertNull($offerQuery->findBySlug($offer->slug()));
        $this->assertNull($offerQuery->findByEmailHash($offer->emailHash()));

        $this->assertEquals(
            $this->offers->module()->offerThrottleQuery()->limit() - 1,
            $this->offers->module()->offerThrottleQuery()->offersLeft($user->id())
        );
    }
}

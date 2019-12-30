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

namespace HireInSocial\Tests\Offers\Application\Integration\Command\Offer;

use HireInSocial\Offers\Application\Command\Offer\RemoveOffer;
use HireInSocial\Offers\Application\Query\Offer\OfferFilter;
use HireInSocial\Tests\Offers\Application\Integration\HireInSocialTestCase;
use HireInSocial\Tests\Offers\Application\MotherObject\Command\Offer\PostOfferMother;
use Ramsey\Uuid\Uuid;

final class RemoveOfferTest extends HireInSocialTestCase
{
    public function test_removing_offer() : void
    {
        $user = $this->systemContext->createUser();
        $this->systemContext->createSpecialization($specialization = 'spec');
        $this->systemContext->offersFacade()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $user->id(), $specialization));

        $offerQuery = $this->systemContext->offersFacade()->offerQuery();

        $offer = $offerQuery->findAll(OfferFilter::allFor($specialization))->first();

        $this->systemContext->offersFacade()->handle(new RemoveOffer($offer->id()->toString(), $user->id()));

        $this->assertEquals(0, $offerQuery->total());
        $this->assertEquals(0, $offerQuery->findAll(OfferFilter::all())->count());
        $this->assertEquals(0, $offerQuery->findAll(OfferFilter::allFor($specialization))->count());
        $this->assertNull($offerQuery->findById($offer->id()->toString()));
        $this->assertNull($offerQuery->findBySlug($offer->slug()));
        $this->assertNull($offerQuery->findByEmailHash($offer->emailHash()));

        $this->assertEquals(
            $this->systemContext->offersFacade()->offerThrottleQuery()->limit() - 1,
            $this->systemContext->offersFacade()->offerThrottleQuery()->offersLeft($user->id())
        );
    }
}

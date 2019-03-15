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

namespace HireInSocial\Tests\Application\Integration\Command\Offer;

use HireInSocial\Application\Command\Offer\RemoveOffer;
use HireInSocial\Application\Query\Offer\OfferFilter;
use HireInSocial\Application\Query\Offer\OfferQuery;
use HireInSocial\Application\Query\Offer\OfferThrottleQuery;
use HireInSocial\Tests\Application\Integration\HireInSocialTestCase;
use HireInSocial\Tests\Application\MotherObject\Command\Offer\PostOfferMother;

final class RemoveOfferTest extends HireInSocialTestCase
{
    public function test_removing_offer()
    {
        $user = $this->systemContext->createUser();
        $this->systemContext->createSpecialization($specialization = 'spec');
        $this->systemContext->system()->handle(PostOfferMother::random($user->id(), $specialization));

        $offerQuery = $this->systemContext->system()->query(OfferQuery::class);

        $offer = $offerQuery->findAll(OfferFilter::allFor($specialization))->first();

        $this->systemContext->system()->handle(new RemoveOffer($offer->id()->toString(), $user->id()));

        $this->assertEquals(0, $offerQuery->total());
        $this->assertEquals(0, $offerQuery->findAll(OfferFilter::all())->count());
        $this->assertEquals(0, $offerQuery->findAll(OfferFilter::allFor($specialization))->count());
        $this->assertNull($offerQuery->findById($offer->id()->toString()));
        $this->assertNull($offerQuery->findBySlug($offer->slug()));
        $this->assertNull($offerQuery->findByEmailHash($offer->emailHash()));

        $this->assertEquals(
            $this->systemContext->system()->query(OfferThrottleQuery::class)->limit() - 1,
            $this->systemContext->system()->query(OfferThrottleQuery::class)->offersLeft($user->id())
        )
        ;
    }
}

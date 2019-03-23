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

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\Offer\Throttling;
use HireInSocial\Application\Query\Offer\OfferFilter;
use HireInSocial\Application\Query\Offer\OfferQuery;
use HireInSocial\Application\Query\Offer\OfferThrottleQuery;
use HireInSocial\Tests\Application\Integration\HireInSocialTestCase;
use HireInSocial\Tests\Application\MotherObject\Command\Offer\PostOfferMother;

final class PostOfferTest extends HireInSocialTestCase
{
    public function test_posting_offer()
    {
        $user = $this->systemContext->createUser();

        $this->systemContext->createSpecialization($specialization = 'spec');
        $this->systemContext->system()->handle(PostOfferMother::randomWithPDF(
            $user->id(),
            $specialization,
            __DIR__ . '/fixtures/blank.pdf'
        ));

        $offer = $this->systemContext->system()->query(OfferQuery::class)->findAll(OfferFilter::allFor($specialization))->first();

        $this->assertEquals(1, $this->systemContext->system()->query(OfferQuery::class)->total());
        $this->assertEquals(
            sprintf('/offer/%s/offer.pdf', $offer->id()->toString()),
            $offer->offerPDF()
        );
        $this->assertTrue($offer->postedBy($user->id()));
    }

    public function test_posting_offer_to_facebook_when_specialization_fb_channel_is_not_set()
    {
        $user = $this->systemContext->createUser();

        $this->systemContext->createSpecialization($specialization = 'spec');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Specialization "spec" does not have facebook channel assigned.');

        $this->systemContext->system()->handle(PostOfferMother::onFB($user->id(), $specialization));
    }

    public function test_posting_offer_too_fast()
    {
        $user = $this->systemContext->createUser();
        $this->systemContext->createSpecialization($specialization = 'spec');

        for ($postedOffers = 0; $postedOffers < Throttling::LIMIT; $postedOffers++) {
            $this->systemContext->system()->handle(PostOfferMother::random($user->id(), $specialization));
        }

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('User "%s" is throttled', $user->id()));

        $this->assertTrue($this->systemContext->system()->query(OfferThrottleQuery::class)->isThrottled($user->id()));

        $this->systemContext->system()->handle(PostOfferMother::random($user->id(), $specialization));
    }
}

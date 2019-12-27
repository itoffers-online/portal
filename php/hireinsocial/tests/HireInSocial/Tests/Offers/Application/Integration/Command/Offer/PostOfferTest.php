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

use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\Offer\Throttling;
use HireInSocial\Offers\Application\Query\Offer\OfferFilter;
use HireInSocial\Tests\Offers\Application\Integration\HireInSocialTestCase;
use HireInSocial\Tests\Offers\Application\MotherObject\Command\Offer\PostOfferMother;

final class PostOfferTest extends HireInSocialTestCase
{
    public function test_posting_offer() : void
    {
        $user = $this->systemContext->createUser();

        $this->systemContext->createSpecialization($specialization = 'spec');
        $this->systemContext->offersFacade()->handle(PostOfferMother::randomWithPDF(
            $user->id(),
            $specialization,
            __DIR__ . '/fixtures/blank.pdf'
        ));

        $offer = $this->systemContext->offersFacade()->offerQuery()->findAll(OfferFilter::allFor($specialization))->first();

        $this->assertEquals(1, $this->systemContext->offersFacade()->offerQuery()->total());
        $this->assertEquals(
            sprintf('/offer/%s/offer.pdf', $offer->id()->toString()),
            $offer->offerPDF()
        );
        $this->assertTrue($offer->postedBy($user->id()));
    }

    public function test_posting_offer_to_facebook_when_specialization_fb_channel_is_not_set() : void
    {
        $user = $this->systemContext->createUser();

        $this->systemContext->createSpecialization($specialization = 'spec');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Specialization "spec" does not have facebook channel assigned.');

        $this->systemContext->offersFacade()->handle(PostOfferMother::onFB($user->id(), $specialization));
    }

    public function test_posting_offer_too_fast() : void
    {
        $user = $this->systemContext->createUser();
        $this->systemContext->createSpecialization($specialization = 'spec');

        for ($postedOffers = 0; $postedOffers < Throttling::LIMIT; $postedOffers++) {
            $this->systemContext->offersFacade()->handle(PostOfferMother::random($user->id(), $specialization));
        }

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('User "%s" is throttled', $user->id()));

        $this->assertTrue($this->systemContext->offersFacade()->offerThrottleQuery()->isThrottled($user->id()));

        $this->systemContext->offersFacade()->handle(PostOfferMother::random($user->id(), $specialization));
    }
}

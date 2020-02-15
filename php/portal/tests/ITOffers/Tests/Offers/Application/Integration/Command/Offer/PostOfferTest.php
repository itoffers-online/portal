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

use ITOffers\Component\CQRS\Exception\Exception;
use ITOffers\Component\EventBus\Infrastructure\InMemory\InMemoryEventBus;
use ITOffers\Offers\Application\Offer\Throttling;
use ITOffers\Offers\Application\Query\Offer\OfferFilter;
use ITOffers\Tests\Offers\Application\Integration\OffersTestCase;
use ITOffers\Tests\Offers\Application\MotherObject\Command\Offer\PostOfferMother;
use Ramsey\Uuid\Uuid;

final class PostOfferTest extends OffersTestCase
{
    public function test_posting_offer() : void
    {
        $user = $this->systemContext->createUser();

        $this->systemContext->createSpecialization($specialization = 'spec');
        $this->systemContext->offersFacade()->handle(PostOfferMother::randomWithPDF(
            Uuid::uuid4()->toString(),
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
        $this->assertSame(InMemoryEventBus::OFFERS_EVENT_OFFER_POST, $this->publishedEvents->lastEvent()->name());
    }

    public function test_posting_offer_too_fast() : void
    {
        $user = $this->systemContext->createUser();
        $this->systemContext->createSpecialization($specialization = 'spec');

        for ($postedOffers = 0; $postedOffers < Throttling::LIMIT; $postedOffers++) {
            $this->systemContext->offersFacade()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $user->id(), $specialization));
        }

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('User "%s" is throttled', $user->id()));

        $this->assertTrue($this->systemContext->offersFacade()->offerThrottleQuery()->isThrottled($user->id()));

        $this->systemContext->offersFacade()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $user->id(), $specialization));
    }

    public function test_posting_offer_too_fast_with_extra_offer() : void
    {
        $user = $this->systemContext->createUser();
        $this->systemContext->createSpecialization($specialization = 'spec');

        $this->systemContext->addExtraOffer($user, $expiresInDays = 1);
        $this->systemContext->addExtraOffer($user, $expiresInDays = 3);

        for ($postedOffers = 0; $postedOffers < Throttling::LIMIT; $postedOffers++) {
            $this->systemContext->offersFacade()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $user->id(), $specialization));
        }

        $this->assertSame(2, $this->systemContext->offersFacade()->extraOffersQuery()->countNotExpired($user->id()));
        $this->assertTrue($this->systemContext->offersFacade()->offerThrottleQuery()->isThrottled($user->id()));

        $this->systemContext->offersFacade()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $user->id(), $specialization));

        $this->assertSame(1, $this->systemContext->offersFacade()->extraOffersQuery()->countNotExpired($user->id()));
        $this->assertGreaterThanOrEqual(
            2,
            $this->systemContext->offersFacade()->extraOffersQuery()->findClosesToExpire($user->id())->expiresAt()->diff(new \DateTimeImmutable())->days
        );
    }
}

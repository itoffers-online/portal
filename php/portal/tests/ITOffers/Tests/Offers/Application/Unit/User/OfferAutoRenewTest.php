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

namespace ITOffers\Tests\Offers\Application\Unit\User;

use ITOffers\Offers\Application\Exception\InvalidAssertionException;
use ITOffers\Offers\Application\User\OfferAutoRenew;
use ITOffers\Tests\Offers\Application\Double\Stub\CalendarStub;
use ITOffers\Tests\Offers\Application\MotherObject\Offer\OfferMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class OfferAutoRenewTest extends TestCase
{
    public function test_assign_expired_auto_renew() : void
    {
        $expiredAutoRenew = OfferAutoRenew::expiresInDays(Uuid::uuid4(), 1, $calendar = new CalendarStub());

        $calendar->addDays(5);

        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Offer renew already expired');

        $expiredAutoRenew->assign(OfferMother::random(), $calendar);
    }

    public function test_assign_already_assigned_offer_renew() : void
    {
        $expiredAutoRenew = OfferAutoRenew::expiresInDays(Uuid::uuid4(), 1, $calendar = new CalendarStub());

        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Offer renew already assigned');

        $expiredAutoRenew->assign(OfferMother::random(), $calendar);
        $expiredAutoRenew->assign(OfferMother::random(), $calendar);
    }

    public function test_using_offer_renew_with_offer_renew() : void
    {
        $offerAutoRenew = OfferAutoRenew::expiresInDays(Uuid::uuid4(), 1, $calendar = new CalendarStub());

        $offerAutoRenew->assign(OfferMother::random(), $calendar);

        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Offer renew was assigned to different offer');

        $offerAutoRenew->renew(OfferMother::random(), $calendar);
    }

    public function test_using_already_used_offer_auto_renew() : void
    {
        $offerAutoRenew = OfferAutoRenew::expiresInDays(Uuid::uuid4(), 1, $calendar = new CalendarStub());

        $offerAutoRenew->assign($offer = OfferMother::random(), $calendar);

        $offerAutoRenew->renew($offer, $calendar);

        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Offer renew already used');

        $offerAutoRenew->renew($offer, $calendar);
    }
}

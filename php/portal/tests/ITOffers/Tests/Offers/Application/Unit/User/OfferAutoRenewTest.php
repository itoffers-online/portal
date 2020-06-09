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

use Aeon\Calendar\Gregorian\GregorianCalendarStub;
use ITOffers\Offers\Application\Exception\InvalidAssertionException;
use ITOffers\Offers\Application\User\OfferAutoRenew;
use ITOffers\Offers\Application\User\OfferAutoRenews;
use ITOffers\Tests\Offers\Application\MotherObject\Offer\OfferMother;
use ITOffers\Tests\Offers\Application\MotherObject\User\UserMother;
use PHPUnit\Framework\TestCase;

final class OfferAutoRenewTest extends TestCase
{
    public function test_assign_expired_auto_renew() : void
    {
        $user = UserMother::random();
        $expiredAutoRenew = OfferAutoRenew::expiresInDays($user->id(), 1, $calendar = new GregorianCalendarStub());

        $calendar->setNow($calendar->now()->addDays(5));

        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Offer renew already expired');

        $expiredAutoRenew->assign(OfferMother::byUser($user), $this->createMock(OfferAutoRenews::class), 20, $calendar);
    }

    public function test_assign_already_assigned_offer_renew() : void
    {
        $user = UserMother::random();
        $expiredAutoRenew = OfferAutoRenew::expiresInDays($user->id(), 1, $calendar = new GregorianCalendarStub());

        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Offer renew already assigned');

        $expiredAutoRenew->assign(OfferMother::byUser($user), $this->createMock(OfferAutoRenews::class), 20, $calendar);
        $expiredAutoRenew->assign(OfferMother::byUser($user), $this->createMock(OfferAutoRenews::class), 20, $calendar);
    }

    public function test_using_offer_renew_with_offer_renew() : void
    {
        $user = UserMother::random();
        $offerAutoRenew = OfferAutoRenew::expiresInDays($user->id(), 1, $calendar = new GregorianCalendarStub());

        $offerAutoRenew->assign(OfferMother::byUser($user), $this->createMock(OfferAutoRenews::class), 20, $calendar);

        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Offer renew was assigned to different offer');

        $offerAutoRenew->renew(OfferMother::byUser($user), $calendar);
    }

    public function test_using_already_used_offer_auto_renew() : void
    {
        $user = UserMother::random();
        $offerAutoRenew = OfferAutoRenew::expiresInDays($user->id(), 1, $calendar = new GregorianCalendarStub());

        $offerAutoRenew->assign($offer = OfferMother::byUser($user), $this->createMock(OfferAutoRenews::class), 20, $calendar);

        $offerAutoRenew->renew($offer, $calendar);

        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Offer renew already used');

        $offerAutoRenew->renew($offer, $calendar);
    }

    public function test_assigning_auto_renew_to_offer_with_already_assigned_auto_renews() : void
    {
        $user = UserMother::random();
        $offerAutoRenew = OfferAutoRenew::expiresInDays($user->id(), 1, $calendar = new GregorianCalendarStub());

        $offerAutoRenews = $this->createMock(OfferAutoRenews::class);
        $offerAutoRenews->method('countAssignedTo')
            ->willReturn(2);

        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('There are already 2 auto renews assigned to that offer.');

        $offerAutoRenew->assign(OfferMother::byUser($user), $offerAutoRenews, 20, $calendar);
    }

    public function test_assigning_auto_renew_to_offer_that_already_expired() : void
    {
        $user = UserMother::random();
        $offerAutoRenew = OfferAutoRenew::expiresInDays($user->id(), 60, $calendar = new GregorianCalendarStub());
        $offer = OfferMother::byUser($user, $calendar);

        $calendar->setNow($calendar->now()->modify('+20 days'));

        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Offer already expired');

        $offerAutoRenew->assign($offer, $this->createMock(OfferAutoRenews::class), 20, $calendar);
    }
}

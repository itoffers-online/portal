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
use ITOffers\Offers\Application\User\OfferAutoRenews;
use ITOffers\Tests\Component\Calendar\Double\Stub\CalendarStub;
use ITOffers\Tests\Offers\Application\MotherObject\Offer\OfferMother;
use ITOffers\Tests\Offers\Application\MotherObject\User\UserMother;
use PHPUnit\Framework\TestCase;

final class OfferAutoRenewTest extends TestCase
{
    public function test_assign_expired_auto_renew() : void
    {
        $user = UserMother::random();
        $expiredAutoRenew = OfferAutoRenew::expiresInDays($user->id(), 1, $calendar = new CalendarStub());

        $calendar->addDays(5);

        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Offer renew already expired');

        $expiredAutoRenew->assign(OfferMother::byUser($user), $this->createMock(OfferAutoRenews::class), new \DateInterval('P1D'), $calendar);
    }

    public function test_assign_already_assigned_offer_renew() : void
    {
        $user = UserMother::random();
        $expiredAutoRenew = OfferAutoRenew::expiresInDays($user->id(), 1, $calendar = new CalendarStub());

        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Offer renew already assigned');

        $expiredAutoRenew->assign(OfferMother::byUser($user), $this->createMock(OfferAutoRenews::class), new \DateInterval('P1D'), $calendar);
        $expiredAutoRenew->assign(OfferMother::byUser($user), $this->createMock(OfferAutoRenews::class), new \DateInterval('P1D'), $calendar);
    }

    public function test_using_offer_renew_with_offer_renew() : void
    {
        $user = UserMother::random();
        $offerAutoRenew = OfferAutoRenew::expiresInDays($user->id(), 1, $calendar = new CalendarStub());

        $offerAutoRenew->assign(OfferMother::byUser($user), $this->createMock(OfferAutoRenews::class), new \DateInterval('P1D'), $calendar);

        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Offer renew was assigned to different offer');

        $offerAutoRenew->renew(OfferMother::byUser($user), $calendar);
    }

    public function test_using_already_used_offer_auto_renew() : void
    {
        $user = UserMother::random();
        $offerAutoRenew = OfferAutoRenew::expiresInDays($user->id(), 1, $calendar = new CalendarStub());

        $offerAutoRenew->assign($offer = OfferMother::byUser($user), $this->createMock(OfferAutoRenews::class), new \DateInterval('P1D'), $calendar);

        $offerAutoRenew->renew($offer, $calendar);

        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Offer renew already used');

        $offerAutoRenew->renew($offer, $calendar);
    }

    public function test_assigning_auto_renew_to_offer_with_already_assigned_auto_renews() : void
    {
        $user = UserMother::random();
        $offerAutoRenew = OfferAutoRenew::expiresInDays($user->id(), 1, $calendar = new CalendarStub());

        $offerAutoRenews = $this->createMock(OfferAutoRenews::class);
        $offerAutoRenews->method('countAssignedTo')
            ->willReturn(2);

        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('There are already 2 auto renews assigned to that offer.');

        $offerAutoRenew->assign(OfferMother::byUser($user), $offerAutoRenews, new \DateInterval('P1D'), $calendar);
    }
}

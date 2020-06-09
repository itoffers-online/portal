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

namespace ITOffers\Tests\Notifications\Application\Unit;

use Aeon\Calendar\Gregorian\DateTime;
use ITOffers\Component\Mailer\Mailer;
use ITOffers\Notifications\Application\Email\EmailFormatter;
use ITOffers\Notifications\Application\Event\ExtraOffersAdded;
use ITOffers\Notifications\Application\Event\OfferPostedEvent;
use ITOffers\Notifications\Application\Offers;
use ITOffers\Notifications\Application\User\User;
use ITOffers\Notifications\Application\Users;
use ITOffers\Notifications\Notifications;
use ITOffers\Tests\Notifications\Application\MotherObject\OfferMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class NotificationsTest extends TestCase
{
    public function test_handle_offer_posted_event() : void
    {
        $offerId = Uuid::uuid4();

        $module = new Notifications(
            $mailer = $this->createMock(Mailer::class),
            $offers = $this->createMock(Offers::class),
            $users = $this->createMock(Users::class),
            $emailFormatter = $this->createMock(EmailFormatter::class),
            'contact@itoffers.online',
            'itoffers.online'
        );

        $offers->method('getById')
            ->with($offerId)
            ->willReturn($offer = OfferMother::random());

        $emailFormatter->method('offerPostedSubject')
            ->with($offer)
            ->willReturn('subject');

        $emailFormatter->method('offerPostedBody')
            ->with($offer)
            ->willReturn('html body');

        $mailer->expects($this->once())
            ->method('send');

        $module->handle(new OfferPostedEvent(
            $eventId = Uuid::uuid4(),
            DateTime::fromString('now'),
            $offerId
        ));
    }

    public function test_handle_extra_offers_added_event() : void
    {
        $userId = Uuid::uuid4();

        $module = new Notifications(
            $mailer = $this->createMock(Mailer::class),
            $offers = $this->createMock(Offers::class),
            $users = $this->createMock(Users::class),
            $emailFormatter = $this->createMock(EmailFormatter::class),
            'contact@itoffers.online',
            'itoffers.online'
        );

        $users->method('getById')
            ->with($userId)
            ->willReturn($user = new User('user@email.com'));

        $emailFormatter->method('extraOffersAddedSubject')
            ->willReturn('subject');

        $emailFormatter->method('extraOffersAddedBody')
            ->with($expiresInDays = 30, $amount = 1)
            ->willReturn('html body');

        $mailer->expects($this->once())
            ->method('send');

        $module->handle(new ExtraOffersAdded(
            $eventId = Uuid::uuid4(),
            DateTime::fromString('now'),
            $userId,
            $expiresInDays,
            $amount
        ));
    }

    public function test_handle_offer_posted_event_when_disabled() : void
    {
        $offerId = Uuid::uuid4();

        $module = new Notifications(
            $mailer = $this->createMock(Mailer::class),
            $offers = $this->createMock(Offers::class),
            $users = $this->createMock(Users::class),
            $emailFormatter = $this->createMock(EmailFormatter::class),
            'contact@itoffers.online',
            'itoffers.online'
        );

        $module->disable();

        $mailer->expects($this->never())
            ->method('send');

        $module->handle(new OfferPostedEvent(
            $eventId = Uuid::uuid4(),
            DateTime::fromString('now'),
            $offerId
        ));
    }
}

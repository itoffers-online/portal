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

namespace ITOffers\Offers\Application\Command\User;

use ITOffers\Component\Calendar\Calendar;
use ITOffers\Component\CQRS\EventStream;
use ITOffers\Component\CQRS\System\Handler;
use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\User\Event\OfferAutoRenewsAdded;
use ITOffers\Offers\Application\User\OfferAutoRenew;
use ITOffers\Offers\Application\User\OfferAutoRenews;
use ITOffers\Offers\Application\User\Users;
use Ramsey\Uuid\Uuid;

final class AddOfferAutoRenewsHandler implements Handler
{
    private Users $users;

    private OfferAutoRenews $offerAutoRenews;

    private EventStream $eventStream;

    private Calendar $calendar;

    public function __construct(Users $users, OfferAutoRenews $offerAutoRenews, EventStream $eventStream, Calendar $calendar)
    {
        $this->users = $users;
        $this->offerAutoRenews = $offerAutoRenews;
        $this->calendar = $calendar;
        $this->eventStream = $eventStream;
    }

    public function handles() : string
    {
        return AddOfferAutoRenews::class;
    }

    public function __invoke(AddOfferAutoRenews $command) : void
    {
        Assertion::greaterOrEqualThan($command->count(), 1);
        $user = $this->users->getById(Uuid::fromString($command->userId()));

        $this->offerAutoRenews->add(...\array_map(
            fn () => OfferAutoRenew::expiresInDays($user->id(), $command->expiresInDays(), $this->calendar),
            \range(1, $command->count())
        ));

        $this->eventStream->record(new OfferAutoRenewsAdded($user, $command->expiresInDays(), $command->count()));
    }
}

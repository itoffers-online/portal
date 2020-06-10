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

use Aeon\Calendar\Gregorian\Calendar;
use ITOffers\Component\CQRS\EventStream;
use ITOffers\Component\CQRS\System\Handler;
use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\User\Event\ExtraOffersAdded;
use ITOffers\Offers\Application\User\ExtraOffer;
use ITOffers\Offers\Application\User\ExtraOffers;
use ITOffers\Offers\Application\User\Users;
use Ramsey\Uuid\Uuid;

final class AddExtraOffersHandler implements Handler
{
    private Users $users;

    private ExtraOffers $extraOffers;

    private EventStream $eventStream;

    private Calendar $calendar;

    public function __construct(Users $users, ExtraOffers $extraOffers, EventStream $eventStream, Calendar $calendar)
    {
        $this->users = $users;
        $this->extraOffers = $extraOffers;
        $this->eventStream = $eventStream;
        $this->calendar = $calendar;
    }

    public function handles() : string
    {
        return AddExtraOffers::class;
    }

    public function __invoke(AddExtraOffers $command) : void
    {
        Assertion::greaterOrEqualThan($command->count(), 1);
        $user = $this->users->getById(Uuid::fromString($command->userId()));

        $this->extraOffers->add(...\array_map(
            fn () => ExtraOffer::expiresInDays($user->id(), $command->expiresInDays(), $this->calendar),
            \range(1, $command->count())
        ));

        $this->eventStream->record(new ExtraOffersAdded($user, $command->expiresInDays(), $command->count()));
    }
}

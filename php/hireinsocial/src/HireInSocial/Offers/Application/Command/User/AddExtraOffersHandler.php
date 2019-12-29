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

namespace HireInSocial\Offers\Application\Command\User;

use HireInSocial\Offers\Application\Assertion;
use HireInSocial\Offers\Application\System\Calendar;
use HireInSocial\Offers\Application\System\Handler;
use HireInSocial\Offers\Application\User\ExtraOffer;
use HireInSocial\Offers\Application\User\ExtraOffers;
use HireInSocial\Offers\Application\User\Users;
use Ramsey\Uuid\Uuid;

class AddExtraOffersHandler implements Handler
{
    /**
     * @var Users
     */
    private $users;

    /**
     * @var ExtraOffers
     */
    private $extraOffers;

    /**
     * @var Calendar
     */
    private $calendar;

    public function __construct(Users $users, ExtraOffers $extraOffers, Calendar $calendar)
    {
        $this->users = $users;
        $this->extraOffers = $extraOffers;
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
            function () use ($user, $command) {
                return ExtraOffer::expiresInDays($user->id(), $command->expiresInDays(), $this->calendar);
            },
            \range(1, $command->count())
        ));
    }
}

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
use ITOffers\Component\CQRS\System\Handler;
use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\User\OfferAutoRenew;
use ITOffers\Offers\Application\User\OfferAutoRenews;
use ITOffers\Offers\Application\User\Users;
use Ramsey\Uuid\Uuid;

final class AddOfferAutoRenewsHandler implements Handler
{
    /**
     * @var Users
     */
    private $users;

    /**
     * @var OfferAutoRenews
     */
    private $offerAutoRenews;

    /**
     * @var Calendar
     */
    private $calendar;

    public function __construct(Users $users, OfferAutoRenews $offerAutoRenews, Calendar $calendar)
    {
        $this->users = $users;
        $this->offerAutoRenews = $offerAutoRenews;
        $this->calendar = $calendar;
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
            function () use ($user, $command) {
                return OfferAutoRenew::expiresInDays($user->id(), $command->expiresInDays(), $this->calendar);
            },
            \range(1, $command->count())
        ));
    }
}

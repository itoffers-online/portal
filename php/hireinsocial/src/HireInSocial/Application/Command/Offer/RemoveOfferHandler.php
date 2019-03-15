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

namespace HireInSocial\Application\Command\Offer;

use HireInSocial\Application\Offer\Offers;
use HireInSocial\Application\System\Calendar;
use HireInSocial\Application\System\Handler;
use HireInSocial\Application\User\Users;
use Ramsey\Uuid\Uuid;

final class RemoveOfferHandler implements Handler
{
    private $users;
    private $offers;
    private $calendar;

    public function __construct(
        Users $users,
        Offers $offers,
        Calendar $calendar
    ) {
        $this->users = $users;
        $this->offers = $offers;
        $this->calendar = $calendar;
    }

    public function handles(): string
    {
        return RemoveOffer::class;
    }

    public function __invoke(RemoveOffer $command) : void
    {
        $user = $this->users->getById(Uuid::fromString($command->userId()));
        $offer = $this->offers->getById(Uuid::fromString($command->offerId()));

        $offer->remove($user, $this->calendar);
    }
}

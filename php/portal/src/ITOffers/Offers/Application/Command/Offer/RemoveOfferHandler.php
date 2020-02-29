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

namespace ITOffers\Offers\Application\Command\Offer;

use ITOffers\Component\Calendar\Calendar;
use ITOffers\Component\CQRS\System\Handler;
use ITOffers\Offers\Application\Offer\Offers;
use ITOffers\Offers\Application\User\Users;
use Ramsey\Uuid\Uuid;

final class RemoveOfferHandler implements Handler
{
    private Users $users;

    private Offers $offers;

    private Calendar $calendar;

    public function __construct(
        Users $users,
        Offers $offers,
        Calendar $calendar
    ) {
        $this->users = $users;
        $this->offers = $offers;
        $this->calendar = $calendar;
    }

    public function handles() : string
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

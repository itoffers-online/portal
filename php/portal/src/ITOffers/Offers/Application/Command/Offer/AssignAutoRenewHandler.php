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
use ITOffers\Offers\Application\User\OfferAutoRenews;
use ITOffers\Offers\Application\User\Users;
use Ramsey\Uuid\Uuid;

final class AssignAutoRenewHandler implements Handler
{
    private Users $users;

    private Offers $offers;

    private OfferAutoRenews $offerAutoRenews;

    private Calendar $calendar;

    public function __construct(Users $users, Offers $offers, OfferAutoRenews $offerAutoRenews, Calendar $calendar)
    {
        $this->users = $users;
        $this->offers = $offers;
        $this->offerAutoRenews = $offerAutoRenews;
        $this->calendar = $calendar;
    }

    public function handles() : string
    {
        return AssignAutoRenew::class;
    }

    public function __invoke(AssignAutoRenew $command) : void
    {
        $offer = $this->offers->getById(Uuid::fromString($command->offerId()));
        $user = $this->users->getById(Uuid::fromString($command->userId()));

        $offerAutoRenew = $this->offerAutoRenews->getUnassignedClosesToExpire($user);

        $offerAutoRenew->assign($offer, $this->offerAutoRenews, new \DateInterval(\sprintf('P%dD', $command->renewAfterDays())), $this->calendar);
    }
}

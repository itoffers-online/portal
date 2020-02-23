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
use Ramsey\Uuid\Uuid;

final class RenewOfferHandler implements Handler
{
    /**
     * @var Offers
     */
    private $offers;

    /**
     * @var OfferAutoRenews
     */
    private $offerAutoRenews;

    /**
     * @var Calendar
     */
    private $calendar;

    public function __construct(
        Offers $offers,
        OfferAutoRenews $offerAutoRenews,
        Calendar $calendar
    ) {
        $this->offers = $offers;
        $this->offerAutoRenews = $offerAutoRenews;
        $this->calendar = $calendar;
    }

    public function handles() : string
    {
        return RenewOffer::class;
    }

    public function __invoke(RenewOffer $command) : void
    {
        $offer = $this->offers->getById(Uuid::fromString($command->offerId()));

        $offer->renew($this->offerAutoRenews, $this->calendar);
    }
}

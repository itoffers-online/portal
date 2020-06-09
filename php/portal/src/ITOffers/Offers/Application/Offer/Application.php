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

namespace ITOffers\Offers\Application\Offer;

use Aeon\Calendar\Gregorian\Calendar;
use Aeon\Calendar\Gregorian\DateTime;
use ITOffers\Offers\Application\Offer\Application\EmailHash;
use Ramsey\Uuid\Uuid;

class Application
{
    private string $id;

    private string $offerId;

    private string $emailHash;

    private DateTime $createdAt;

    private function __construct()
    {
    }

    public static function forOffer(EmailHash $email, Offer $offer, Calendar $calendar) : self
    {
        $application = new self();
        $application->id = Uuid::uuid4()->toString();
        $application->offerId = $offer->id()->toString();
        $application->emailHash = $email->toString();
        $application->createdAt = $calendar->now();

        return $application;
    }
}

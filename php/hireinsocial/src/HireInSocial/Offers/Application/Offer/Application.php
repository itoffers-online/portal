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

namespace HireInSocial\Offers\Application\Offer;

use HireInSocial\Offers\Application\Offer\Application\EmailHash;
use HireInSocial\Offers\Application\System\Calendar;
use Ramsey\Uuid\Uuid;

class Application
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $offerId;

    /**
     * @var string
     */
    private $emailHash;

    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    private function __construct()
    {
    }

    public static function forOffer(EmailHash $email, Offer $offer, Calendar $calendar) : self
    {
        $application = new self();
        $application->id = Uuid::uuid4()->toString();
        $application->offerId = $offer->id()->toString();
        $application->emailHash = $email->toString();
        $application->createdAt = $calendar->currentTime();

        return $application;
    }
}

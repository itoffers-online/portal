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

namespace HireInSocial\Application\Offer;

use HireInSocial\Application\System\Calendar;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class OfferPDF
{
    private $id;
    private $path;
    private $offerId;
    private $createdAt;

    private function __construct($path, UuidInterface $offerId, \DateTimeImmutable $createdAt)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->offerId = $offerId->toString();
        $this->path = $path;
        $this->createdAt = $createdAt;
    }

    public static function forOffer(Offer $offer, Calendar $calendar) : self
    {
        return new self(
            sprintf('/offer/%s/offer.pdf', $offer->id()->toString()),
            $offer->id(),
            $calendar->currentTime()
        );
    }

    public function path()
    {
        return $this->path;
    }
}

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
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class OfferPDF
{
    private string $id;

    private string $path;

    private string $offerId;

    private DateTime $createdAt;

    private function __construct(string $path, UuidInterface $offerId, DateTime $createdAt)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->offerId = $offerId->toString();
        $this->path = $path;
        $this->createdAt = $createdAt;
    }

    public static function forOffer(Offer $offer, Slug $slug, Calendar $calendar) : self
    {
        return new self(
            sprintf('/offer/%s/offer.pdf', (string) $slug),
            $offer->id(),
            $calendar->now()
        );
    }

    public function path() : string
    {
        return $this->path;
    }
}

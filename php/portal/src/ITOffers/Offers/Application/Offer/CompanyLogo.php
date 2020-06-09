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

use Cocur\Slugify\Slugify;
use Aeon\Calendar\Gregorian\Calendar;
use ITOffers\Offers\Application\Assertion;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CompanyLogo
{
    private string $id;

    private string $path;

    private string $offerId;

    private \Aeon\Calendar\Gregorian\DateTime $createdAt;

    private function __construct(string $path, UuidInterface $offerId, \Aeon\Calendar\Gregorian\DateTime $createdAt)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->offerId = $offerId->toString();
        $this->path = $path;
        $this->createdAt = $createdAt;
    }

    public static function forOffer(string $format, Offer $offer, Slug $slug, Calendar $calendar) : self
    {
        Assertion::inArray(\mb_strtolower($format), ['jpg', 'jpeg', 'png']);

        $slugify = new Slugify();

        return new self(
            sprintf('/offer/%s/%s.%s', (string) $slug, $slugify->slugify(\mb_strtolower($offer->company()->name())), \mb_strtolower($format)),
            $offer->id(),
            $calendar->now()
        );
    }

    public function path() : string
    {
        return $this->path;
    }
}

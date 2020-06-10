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

namespace ITOffers\Offers\Application\User;

use Aeon\Calendar\Gregorian\Calendar;
use Aeon\Calendar\Gregorian\DateTime;
use Aeon\Calendar\TimeUnit;
use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\Offer\Offer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ExtraOffer
{
    private string $id;

    private string $userId;

    private DateTime $createdAt;

    private DateTime $expiresAt;

    private ?DateTime $usedAt = null;

    private ?string $offerId = null;

    public function __construct(UuidInterface $userId, TimeUnit $expiresIn, Calendar $calendar)
    {
        Assertion::false($expiresIn->isNegative(), "Expires in interval can't be negative");

        $this->id = Uuid::uuid4()->toString();
        $this->userId = $userId->toString();
        $this->expiresAt = $calendar->now()->add($expiresIn);

        $this->createdAt = $calendar->now();
    }

    public static function expiresInDays(UuidInterface $userId, int $days, Calendar $calendar) : self
    {
        return new self($userId, TimeUnit::days($days), $calendar);
    }

    public function useFor(Offer $offer, Calendar $calendar) : void
    {
        Assertion::null($this->usedAt, "Extra offer already used");
        Assertion::true($this->expiresAt->isAfterOrEqual($calendar->now()), "Extra offer expired");
        Assertion::true($offer->userId()->equals($this->userId()), "Offer does not belongs to owner of extra offer");

        $this->usedAt = $calendar->now();
        $this->offerId = $offer->id()->toString();
    }

    public function userId() : UuidInterface
    {
        return Uuid::fromString($this->userId);
    }

    public function isUsed() : bool
    {
        return $this->usedAt !== null;
    }
}

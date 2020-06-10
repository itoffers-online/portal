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

class OfferAutoRenew
{
    private const MAX_OFFER_AUTO_RENEWS = 2;

    private string $id;

    private string $userId;

    private DateTime $expiresAt;

    private DateTime $createdAt;

    private ?DateTime $renewAfter = null;

    private ?string $offerId = null;

    private ?DateTime $renewedAt = null;

    private function __construct(UuidInterface $userId, TimeUnit $expiresIn, Calendar $calendar)
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

    public function assign(Offer $offer, OfferAutoRenews $offerAutoRenews, int $offerLifetimeDays, Calendar $calendar) : void
    {
        Assertion::null($this->offerId, "Offer renew already assigned");
        Assertion::greaterThan($offerLifetimeDays, 0, "Offer lifetime days can't be negative");
        Assertion::true($offer->userId()->equals(Uuid::fromString($this->userId)), 'Offer doesn\'t belong to auto renew owner.');
        Assertion::true($this->expiresAt->isAfterOrEqual($calendar->now()), "Offer renew already expired");
        Assertion::lessThan($offerAutoRenews->countAssignedTo($offer), self::MAX_OFFER_AUTO_RENEWS, "There are already 2 auto renews assigned to that offer.");

        $renewAfterDays = $offerLifetimeDays - $calendar->now()->distanceFrom($offer->createdAt())->inDays();

        Assertion::greaterThan($renewAfterDays, 0, 'Offer already expired');

        $this->offerId = $offer->id()->toString();
        $this->renewAfter = $calendar->now()->add(TimeUnit::days($renewAfterDays));
    }

    public function renew(Offer $offer, Calendar $calendar) : void
    {
        Assertion::true(Uuid::fromString($this->offerId)->equals($offer->id()), "Offer renew was assigned to different offer");
        Assertion::null($this->renewedAt, "Offer renew already used");

        $this->renewedAt = $calendar->now();
    }
}

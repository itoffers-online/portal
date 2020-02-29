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

use ITOffers\Component\Calendar\Calendar;
use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\Offer\Offer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ExtraOffer
{
    private string $id;

    private string $userId;

    private \DateTimeImmutable $createdAt;

    private \DateTimeImmutable $expiresAt;

    private ?\DateTimeImmutable $usedAt = null;

    private ?string $offerId = null;

    public function __construct(UuidInterface $userId, \DateInterval $expiresIn, Calendar $calendar)
    {
        Assertion::same($expiresIn->invert, 0, "Expires in interval can't be negative");

        $this->id = Uuid::uuid4()->toString();
        $this->userId = $userId->toString();
        $this->expiresAt = $calendar->currentTime()->add($expiresIn);

        $this->createdAt = $calendar->currentTime();
    }

    public static function expiresInDays(UuidInterface $userId, int $days, Calendar $calendar) : self
    {
        return new self($userId, new \DateInterval(\sprintf('P%dD', $days)), $calendar);
    }

    public function useFor(Offer $offer, Calendar $calendar) : void
    {
        Assertion::null($this->usedAt, "Extra offer already used");
        Assertion::true($this->expiresAt >= $calendar->currentTime(), "Extra offer expired");
        Assertion::true($offer->getUserId()->equals($this->userId()), "Offer does not belongs to owner of extra offer");

        $this->usedAt = $calendar->currentTime();
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

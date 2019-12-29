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

namespace HireInSocial\Offers\Application\User;

use HireInSocial\Offers\Application\Assertion;
use HireInSocial\Offers\Application\Offer\Offer;
use HireInSocial\Offers\Application\System\Calendar;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ExtraOffer
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable
     */
    private $expiresAt;

    /**
     * @var \DateTimeImmutable
     */
    private $usedAt;

    /**
     * @var string
     */
    private $offerId;

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

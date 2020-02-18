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

use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\Calendar;
use ITOffers\Offers\Application\Offer\Offer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class OfferAutoRenew
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
    private $expiresAt;

    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var UuidInterface|null
     */
    private $offerId;

    /**
     * @var \DateTimeImmutable
     */
    private $renewedAt;

    private function __construct(UuidInterface $userId, \DateInterval $expiresIn, Calendar $calendar)
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

    public function assign(Offer $offer, Calendar $calendar) : void
    {
        Assertion::null($this->offerId, "Offer renew already assigned");
        Assertion::true($this->expiresAt >= $calendar->currentTime(), "Offer renew already expired");
        $this->offerId = $offer->id();
    }

    public function renew(Offer $offer, Calendar $calendar) : void
    {
        Assertion::true($this->offerId->equals($offer->id()), "Offer renew was assigned to different offer");
        Assertion::null($this->renewedAt, "Offer renew already used");

        $this->renewedAt = $calendar->currentTime();
    }
}

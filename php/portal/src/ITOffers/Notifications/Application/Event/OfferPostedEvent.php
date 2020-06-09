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

namespace ITOffers\Notifications\Application\Event;

use ITOffers\Notifications\Application\Event;
use Ramsey\Uuid\UuidInterface;

final class OfferPostedEvent implements Event
{
    private UuidInterface $eventId;

    private \Aeon\Calendar\Gregorian\DateTime $occurredAt;

    private UuidInterface $offerId;

    public function __construct(UuidInterface $eventId, \Aeon\Calendar\Gregorian\DateTime $occurredAt, UuidInterface $offerId)
    {
        $this->eventId = $eventId;
        $this->occurredAt = $occurredAt;
        $this->offerId = $offerId;
    }

    public function id() : UuidInterface
    {
        return $this->eventId;
    }

    public function occurredAt() : \Aeon\Calendar\Gregorian\DateTime
    {
        return $this->occurredAt;
    }

    public function offerId() : UuidInterface
    {
        return $this->offerId;
    }
}

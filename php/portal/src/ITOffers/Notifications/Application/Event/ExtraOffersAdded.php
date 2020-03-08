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

final class ExtraOffersAdded implements Event
{
    private UuidInterface $eventId;

    private \DateTimeImmutable $occurredAt;

    private UuidInterface $userId;

    private int $expiresInDays;

    private int $amount;

    public function __construct(UuidInterface $eventId, \DateTimeImmutable $occurredAt, UuidInterface $userId, int $expiresInDays, int $amount)
    {
        $this->eventId = $eventId;
        $this->occurredAt = $occurredAt;
        $this->userId = $userId;
        $this->expiresInDays = $expiresInDays;
        $this->amount = $amount;
    }

    public function id() : UuidInterface
    {
        return $this->eventId;
    }

    public function occurredAt() : \DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function eventId() : UuidInterface
    {
        return $this->eventId;
    }

    public function userId() : UuidInterface
    {
        return $this->userId;
    }

    public function expiresInDays() : int
    {
        return $this->expiresInDays;
    }

    public function amount() : int
    {
        return $this->amount;
    }
}

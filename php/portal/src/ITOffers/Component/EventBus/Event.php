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

namespace ITOffers\Component\EventBus;

use Ramsey\Uuid\UuidInterface;

final class Event
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var UuidInterface
     */
    private $eventId;

    /**
     * @var \DateTimeImmutable
     */
    private $occurredAt;

    /**
     * @var array
     */
    private $payload;

    public function __construct(UuidInterface $id, \DateTimeImmutable $occurredAt, string $name, array $payload)
    {
        $this->eventId = $id;
        $this->occurredAt = $occurredAt;
        $this->name = $name;
        $this->payload = $payload;
    }

    public function id() : UuidInterface
    {
        return $this->eventId;
    }

    public function occurredAt() : \DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function payload() : array
    {
        return $this->payload;
    }
}

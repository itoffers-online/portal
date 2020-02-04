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

namespace HireInSocial\Notifications\Application\Event;

use HireInSocial\Notifications\Application\Event;
use Ramsey\Uuid\UuidInterface;

final class NewOfferPosted implements Event
{
    /**
     * @var UuidInterface
     */
    private $eventId;

    /**
     * @var \DateTimeImmutable
     */
    private $occurredAt;

    /**
     * @var UuidInterface
     */
    private $offerId;

    public function __construct(UuidInterface $eventId, \DateTimeImmutable $occurredAt, UuidInterface $offerId)
    {
        $this->eventId = $eventId;
        $this->occurredAt = $occurredAt;
        $this->offerId = $offerId;
    }

    public function id() : UuidInterface
    {
        return $this->eventId;
    }

    public function occurredAt() : \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}

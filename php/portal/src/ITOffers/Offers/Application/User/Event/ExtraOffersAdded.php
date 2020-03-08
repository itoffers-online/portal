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

namespace ITOffers\Offers\Application\User\Event;

use ITOffers\Component\CQRS\EventStream\Event;
use ITOffers\Offers\Application\User\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class ExtraOffersAdded implements Event
{
    private UuidInterface $id;

    private \DateTimeImmutable $occurredAt;

    private array $payload;

    public function __construct(User $user, int $expiresInDays, int $amount)
    {
        $this->id = Uuid::uuid4();
        $this->occurredAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->payload = [
            'userId' => $user->id()->toString(),
            'expiresInDays' => $expiresInDays,
            'amount' => $amount,
        ];
    }

    public function id() : UuidInterface
    {
        return $this->id;
    }

    public function occurredAt() : \DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function payload() : array
    {
        return $this->payload;
    }
}

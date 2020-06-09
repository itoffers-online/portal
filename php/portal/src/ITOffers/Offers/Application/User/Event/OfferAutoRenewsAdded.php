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

use Aeon\Calendar\Gregorian\DateTime;
use Aeon\Calendar\Gregorian\GregorianCalendar;
use ITOffers\Component\CQRS\EventStream\Event;
use ITOffers\Offers\Application\User\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class OfferAutoRenewsAdded implements Event
{
    private UuidInterface $id;

    private DateTime $occurredAt;

    private array $payload;

    public function __construct(User $user, int $expiresInDays, int $amount)
    {
        $this->id = Uuid::uuid4();
        $this->occurredAt = GregorianCalendar::UTC()->now();
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

    public function occurredAt() : DateTime
    {
        return $this->occurredAt;
    }

    public function payload() : array
    {
        return $this->payload;
    }
}

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

namespace ITOffers\Offers\Application\Offer\Event;

use ITOffers\Component\CQRS\EventStream\Event;
use ITOffers\Offers\Application\Offer\Offer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class OfferPostedEvent implements Event
{
    private UuidInterface $id;

    private \DateTimeImmutable $occurredAt;

    private array $payload;

    public function __construct(Offer $offer)
    {
        $this->id = Uuid::uuid4();
        $this->occurredAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->payload = [
            'offerId' => $offer->id()->toString(),
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

    /**
     * @return array
     */
    public function payload() : array
    {
        return $this->payload;
    }
}

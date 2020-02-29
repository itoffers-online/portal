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

namespace ITOffers\Offers\Application\Query\User\Model;

final class UnassignedAutoRenew
{
    private string $userId;

    private \DateTimeImmutable $expiresAt;

    public function __construct(string $userId, \DateTimeImmutable $expiresAt)
    {
        $this->userId = $userId;
        $this->expiresAt = $expiresAt;
    }

    public function expiresAt() : \DateTimeImmutable
    {
        return $this->expiresAt;
    }
}

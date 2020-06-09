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

final class ExtraOffer
{
    private string $userId;

    private \Aeon\Calendar\Gregorian\DateTime $expiresAt;

    public function __construct(string $userId, \Aeon\Calendar\Gregorian\DateTime $expiresAt)
    {
        $this->userId = $userId;
        $this->expiresAt = $expiresAt;
    }

    public function expiresAt() : \Aeon\Calendar\Gregorian\DateTime
    {
        return $this->expiresAt;
    }
}

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

use Ramsey\Uuid\UuidInterface;

interface OfferAutoRenews
{
    public function add(OfferAutoRenew ...$offerAutoRenews) : void;

    public function findUnassignedClosesToExpire(UuidInterface $userId) : ?OfferAutoRenew;

    public function countNotAssignedNotExpired(UuidInterface $userId) : int;
}

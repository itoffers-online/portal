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

namespace ITOffers\Offers\Application\Query\User;

use ITOffers\Component\CQRS\System\Query;
use ITOffers\Offers\Application\Query\User\Model\OfferAutoRenew;

interface OfferAutoRenewQuery extends Query
{
    public function countUnassignedNotExpired(string $userId) : int;

    public function findUnassignedClosesToExpire(string $userId) : ?OfferAutoRenew;
}

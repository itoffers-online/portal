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

namespace ITOffers\Offers\Application\Query\Offer;

use ITOffers\Component\CQRS\System\Query;

interface ApplicationQuery extends Query
{
    public function alreadyApplied(string $offerId, string $email) : bool;

    public function countFor(string $offerId) : int;
}

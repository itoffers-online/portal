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

namespace HireInSocial\Application\Query\Offer;

use HireInSocial\Application\System\Query;

interface OfferThrottleQuery extends Query
{
    public function limit() : int;

    public function since() : \DateInterval;

    public function isThrottled(string $userId) : bool;

    public function offersLeft(string $userId) : int;
}

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

use HireInSocial\Application\Offer\Throttle;
use HireInSocial\Application\System\Query;

final class OfferThrottleQuery implements Query
{
    private $throttle;

    public function __construct(Throttle $throttle)
    {
        $this->throttle = $throttle;
    }

    public function isThrottled(string $id) : bool
    {
        return $this->throttle->isThrottled($id);
    }
}

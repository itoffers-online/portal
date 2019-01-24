<?php

declare(strict_types=1);

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

<?php

declare (strict_types=1);

namespace HireInSocial\Application\Offer;

interface Throttle
{
    public function isThrottled(string $id) : bool;
    public function throttle(string $id) : void;
}
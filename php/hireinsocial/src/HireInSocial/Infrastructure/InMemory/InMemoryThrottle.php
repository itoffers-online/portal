<?php

declare(strict_types=1);

namespace HireInSocial\Infrastructure\InMemory;

use HireInSocial\Application\Offer\Throttle;

final class InMemoryThrottle implements Throttle
{
    private $throttles = [];

    public function isThrottled(string $id): bool
    {
        return isset($this->throttles[$id]);
    }

    public function throttle(string $id): void
    {
        $this->throttles[$id] = true;
    }
}

<?php

declare(strict_types=1);

namespace HireInSocial\Infrastructure\InMemory\Application;

use HireInSocial\Application\Offer\Throttle;

final class InMemoryThrottle implements Throttle
{
    private $throttles = [];

    public function isThrottled(string $id): bool
    {
        return \array_key_exists($id, $this->throttles);
    }

    public function throttle(string $id): void
    {
        $this->throttles[$id] = true;
    }

    public function remove(string $id): void
    {
        if (\array_key_exists($id, $this->throttles)) {
            unset($this->throttles[$id]);
        }
    }

    public function flush() : void
    {
        $this->throttles = [];
    }
}

<?php

declare(strict_types=1);

namespace HireInSocial\Infrastructure\Predis;

use HireInSocial\Application\Offer\Throttle;
use HireInSocial\Application\System\Calendar;
use Predis\Client;

final class PredisThrottle implements Throttle
{
    private $client;
    private $calendar;
    private $throttleDuration;
    private $throttlePrefix;

    public function __construct(Client $client, Calendar $calendar, \DateInterval $throttleDuration, string $throttlePrefix)
    {
        $this->client = $client;
        $this->calendar = $calendar;
        $this->throttleDuration = $throttleDuration;
        $this->throttlePrefix = $throttlePrefix;
    }

    public function isThrottled(string $id): bool
    {
        return (bool) $this->client->exists($this->cacheKey($id));
    }

    public function throttle(string $id): void
    {
        $ttl = (int) \DateTime::createFromFormat('U', '0')->add($this->throttleDuration)->format('U');

        $this->client->setex(
            $this->cacheKey($id),
            $ttl,
            $this->calendar->currentTime()->format(\DateTimeInterface::ATOM)
        );
    }

    private function cacheKey(string $id): string
    {
        return sprintf('%s%s', $this->throttlePrefix, $id);
    }
}

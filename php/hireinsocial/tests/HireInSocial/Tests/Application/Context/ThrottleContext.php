<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Context;

use Predis\Client;

final class ThrottleContext
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function purgeThrottles()
    {
        $this->client->flushall();
    }
}

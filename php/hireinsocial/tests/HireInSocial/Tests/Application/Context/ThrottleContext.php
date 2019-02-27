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

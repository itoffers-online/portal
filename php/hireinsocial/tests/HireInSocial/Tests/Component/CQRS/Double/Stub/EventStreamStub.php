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

namespace HireInSocial\Tests\Component\CQRS\Double\Stub;

use HireInSocial\Component\CQRS\EventStream;
use HireInSocial\Component\CQRS\EventStream\Event;

final class EventStreamStub implements EventStream
{
    /**
     * @var array
     */
    private $events;

    public function __construct()
    {
        $this->events = [];
    }

    public function record(Event $event) : void
    {
        $this->events[] = $event;
    }

    public function flush() : void
    {
        $this->events = [];
    }
}

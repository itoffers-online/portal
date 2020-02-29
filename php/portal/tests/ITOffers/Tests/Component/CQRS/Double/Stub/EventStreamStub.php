<?php

declare(strict_types=1);

/*
 * This file is part of the itoffers.online project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ITOffers\Tests\Component\CQRS\Double\Stub;

use ITOffers\Component\CQRS\EventStream;
use ITOffers\Component\CQRS\EventStream\Event;

final class EventStreamStub implements EventStream
{
    private array

 $events;

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

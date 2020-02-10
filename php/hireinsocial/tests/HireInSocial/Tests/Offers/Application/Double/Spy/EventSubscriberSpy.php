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

namespace HireInSocial\Tests\Offers\Application\Double\Spy;

use HireInSocial\Component\EventBus\Event;
use HireInSocial\Component\EventBus\Subscriber;

final class EventSubscriberSpy implements Subscriber
{
    /**
     * @var Event[]
     */
    private $events;

    public function __construct()
    {
        $this->events = [];
    }

    public function receive(Event $event) : void
    {
        $this->events[] = $event;
    }

    public function lastEvent() : ?Event
    {
        return \array_pop($this->events);
    }
}

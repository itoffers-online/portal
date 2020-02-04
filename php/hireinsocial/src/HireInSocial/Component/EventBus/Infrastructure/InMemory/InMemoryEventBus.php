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

namespace HireInSocial\Component\EventBus\Infrastructure\InMemory;

use HireInSocial\Component\EventBus\Event;
use HireInSocial\Component\EventBus\Subscriber;

final class InMemoryEventBus
{
    /**
     * @var array<array<Subscriber>>
     */
    private $topics;

    public function __construct()
    {
        $this->topics = [];
    }

    public function publishTo(string $topic, Event $event) : void
    {
        if (\array_key_exists($topic, $this->topics)) {
            /** @var Subscriber $subscriber */
            foreach ($this->topics as $subscriber) {
                $subscriber->receive($event);
            }
        }
    }

    public function registerTo(string $topic, Subscriber $subscriber) : void
    {
        if (!\array_key_exists($topic, $this->topics)) {
            $this->topics[$topic] = [];
        }

        $this->topics[$topic][] = $subscriber;
    }
}

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

namespace ITOffers\Component\EventBus\Infrastructure\InMemory;

use ITOffers\Component\EventBus\Event;
use ITOffers\Component\EventBus\Subscriber;

final class InMemoryEventBus
{
    public const TOPIC_OFFERS = 'offers';

    public const OFFERS_EVENT_OFFER_POST = 'offer_posted';

    /**
     * @var array<array<Subscriber>>
     */
    private array $topics;

    public function __construct()
    {
        $this->topics = [];
    }

    public function publishTo(string $topic, Event $event) : void
    {
        if (\array_key_exists($topic, $this->topics)) {
            /** @var Subscriber $subscriber */
            foreach ($this->topics[$topic] as $subscriber) {
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

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

namespace HireInSocial\Notifications\Infrastructure;

use HireInSocial\Component\EventBus\Event;
use HireInSocial\Component\EventBus\Infrastructure\InMemory\InMemoryEventBus;
use HireInSocial\Component\EventBus\Subscriber;
use HireInSocial\Component\Mailer\Mailer;
use HireInSocial\Config;
use HireInSocial\Notifications\Application\Event\OfferPostedEvent;
use HireInSocial\Notifications\Application\Exception\Exception;
use HireInSocial\Notifications\Notifications;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

function notificationsFacade(Config $config, InMemoryEventBus $eventBus, Mailer $mailer, LoggerInterface $logger) : Notifications
{
    $notifications = new Notifications($mailer);

    $eventBus->registerTo('offers', new class($notifications, $logger) implements Subscriber {
        /**
         * @var Notifications
         */
        private $notifications;

        /**
         * @var LoggerInterface
         */
        private $logger;

        public function __construct(Notifications $notifications, LoggerInterface $logger)
        {
            $this->notifications = $notifications;
            $this->logger = $logger;
        }

        public function receive(Event $event) : void
        {
            switch ($event->name()) {
                case InMemoryEventBus::OFFERS_EVENT_OFFER_POST:
                    $this->notifications->handle(
                        new OfferPostedEvent(
                            $event->id(),
                            $event->occurredAt(),
                            Uuid::fromString($event->payload()['offerId'])
                        )
                    );
                    $this->logger->debug('offer_posted event received');

                    break;
                default:
                    throw new Exception(\sprintf('Unknown event %s', $event->name()));
            }
        }
    });

    return $notifications;
}

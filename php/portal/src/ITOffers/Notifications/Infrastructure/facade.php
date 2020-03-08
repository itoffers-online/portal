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

namespace ITOffers\Notifications\Infrastructure;

use ITOffers\Component\EventBus\Event;
use ITOffers\Component\EventBus\Infrastructure\InMemory\InMemoryEventBus;
use ITOffers\Component\EventBus\Subscriber;
use ITOffers\Component\Mailer\Mailer;
use ITOffers\Config;
use ITOffers\Notifications\Application\Event\ExtraOffersAdded;
use ITOffers\Notifications\Application\Event\OfferAutoRenewsAdded;
use ITOffers\Notifications\Application\Event\OfferPostedEvent;
use ITOffers\Notifications\Application\Exception\Exception;
use ITOffers\Notifications\Infrastructure\Offers\ModuleOffers;
use ITOffers\Notifications\Infrastructure\Twig\TwigEmailFormatter;
use ITOffers\Notifications\Infrastructure\Users\ModuleOffersUsers;
use ITOffers\Notifications\Notifications;
use ITOffers\Offers\Offers;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Twig\Environment;

function notificationsFacade(Config $config, InMemoryEventBus $eventBus, Offers $offersModule, Mailer $mailer, Environment $twig, LoggerInterface $logger) : Notifications
{
    $notifications = new Notifications(
        $mailer,
        new ModuleOffers($offersModule),
        new ModuleOffersUsers($offersModule),
        new TwigEmailFormatter($twig),
        $config->getString(Config::CONTACT_EMAIL),
        $config->getString(Config::DOMAIN)
    );

    $eventBus->registerTo('offers', new class($notifications, $logger) implements Subscriber {
        private Notifications $notifications;

        private LoggerInterface $logger;

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
                    $this->logger->debug(InMemoryEventBus::OFFERS_EVENT_OFFER_POST . ' event received');

                    break;
                case InMemoryEventBus::OFFERS_EVENT_USER_EXTRA_OFFERS_ADDED:
                    $this->notifications->handle(
                        new ExtraOffersAdded(
                            $event->id(),
                            $event->occurredAt(),
                            Uuid::fromString($event->payload()['userId']),
                            $event->payload()['expiresInDays'],
                            $event->payload()['amount']
                        )
                    );
                    $this->logger->debug(InMemoryEventBus::OFFERS_EVENT_USER_EXTRA_OFFERS_ADDED . ' event received');

                    break;
                case InMemoryEventBus::OFFERS_EVENT_USER_OFFER_AUTO_RENEW_ADDED:
                    $this->notifications->handle(
                        new OfferAutoRenewsAdded(
                            $event->id(),
                            $event->occurredAt(),
                            Uuid::fromString($event->payload()['userId']),
                            $event->payload()['expiresInDays'],
                            $event->payload()['amount']
                        )
                    );
                    $this->logger->debug(InMemoryEventBus::OFFERS_EVENT_USER_OFFER_AUTO_RENEW_ADDED . ' event received');

                    break;
                default:
                    throw new Exception(\sprintf('Unknown event %s', $event->name()));
            }
        }
    });

    return $notifications;
}

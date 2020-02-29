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

namespace ITOffers\Notifications;

use ITOffers\Component\Mailer\Email;
use ITOffers\Component\Mailer\Mailer;
use ITOffers\Component\Mailer\Recipient;
use ITOffers\Component\Mailer\Recipients;
use ITOffers\Component\Mailer\Sender;
use ITOffers\Notifications\Application\Email\EmailFormatter;
use ITOffers\Notifications\Application\Event;
use ITOffers\Notifications\Application\Event\OfferPostedEvent;
use ITOffers\Notifications\Application\Exception\Exception;
use ITOffers\Notifications\Application\Offers;

/**
 * Module - Notifications
 *
 * This module is responsible for sending notifications to the users through available channels like for example:
 *
 *  * Email
 *  * TextMessage
 *  * Facebook Messenger
 *
 * It's listening to all events from other modules and deciding if someone should be notified. Notification
 * content might require data from other modules since events are supposed to simple, without too many details.
 */
final class Notifications
{
    private bool $disabled;

    private Mailer $mailer;

    private Offers $offers;

    private EmailFormatter $emailFormatter;

    private string $contactEmail;

    private string $domain;

    public function __construct(
        Mailer $mailer,
        Offers $offers,
        EmailFormatter $emailFormatter,
        string $contactEmail,
        string $domain
    ) {
        $this->disabled = false;
        $this->mailer = $mailer;
        $this->offers = $offers;
        $this->emailFormatter = $emailFormatter;
        $this->contactEmail = $contactEmail;
        $this->domain = $domain;
    }

    public function handle(Event $event) : void
    {
        if ($this->disabled) {
            return ;
        }

        switch (\get_class($event)) {
            case OfferPostedEvent::class:

                $offer = $this->offers->getById($event->offerId());
                $this->mailer->send(
                    new Email(
                        $this->emailFormatter->offerPostedSubject($offer),
                        $this->emailFormatter->offerPostedBody($offer)
                    ),
                    new Sender(
                        $this->contactEmail,
                        $this->domain,
                        $this->contactEmail
                    ),
                    new Recipients(
                        new Recipient($offer->recruiterEmail(), $offer->recruiterName())
                    )
                );

                break;
            default:
                throw new Exception(\sprintf("Unknown event %s", \get_class($event)));
        }
    }

    public function disable() : void
    {
        $this->disabled = true;
    }
}

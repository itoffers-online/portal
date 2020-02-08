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

namespace HireInSocial\Notifications;

use HireInSocial\Component\Mailer\Mailer;
use HireInSocial\Notifications\Application\Event;
use HireInSocial\Notifications\Application\Exception\Exception;

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
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(Event $event) : void
    {
        switch (\get_class($event)) {
            case Event\OfferPostedEvent::class:
                // Send Email
                break;
            default:
                throw new Exception(\sprintf("Unknown event %s", \get_class($event)));
        }
    }
}

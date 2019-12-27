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

namespace HireInSocial\Offers\Infrastructure\SwiftMailer\System;

use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\System\Mailer;
use HireInSocial\Offers\Application\System\Mailer\Attachments;
use HireInSocial\Offers\Application\System\Mailer\Email;
use HireInSocial\Offers\Application\System\Mailer\Recipients;
use HireInSocial\Offers\Application\System\Mailer\Sender;

final class SwiftMailer implements Mailer
{
    /**
     * @var string
     */
    private $domain;

    /**
     * @var \Swift_Mailer
     */
    private $swiftMailer;

    public function __construct(string $domain, \Swift_Mailer $swiftMailer)
    {
        $this->domain = $domain;
        $this->swiftMailer = $swiftMailer;
    }

    public function domain() : string
    {
        return $this->domain;
    }

    /**
     * @throws Exception
     */
    public function send(Email $email, Sender $sender, Recipients $recipients, Attachments $attachments) : void
    {
        $message = (new \Swift_Message($email->subject()))
            ->setFrom([$sender->email() => $sender->name()])
            ->setBody($email->htmlBody(), 'text/html')
            ->setReplyTo($sender->replyEmail())
        ;

        foreach ($recipients as $recipient) {
            $recipient->isBCC()
                ? $message->addBcc($recipient->email(), $recipient->name())
                : $message->addTo($recipient->email(), $recipient->name());
        }

        foreach ($attachments as $attachment) {
            $message->attach(\Swift_Attachment::fromPath($attachment->filePath()));
        }

        if (!$this->swiftMailer->send($message)) {
            throw new Exception('Could not send email.');
        }
    }
}

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

namespace HireInSocial\Offers\Application\Command\Offer;

use HireInSocial\Offers\Application\Command\Offer\Apply\Attachment;
use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\Hash\Encoder;
use HireInSocial\Offers\Application\Offer\Application;
use HireInSocial\Offers\Application\Offer\Application\EmailHash;
use HireInSocial\Offers\Application\Offer\Applications;
use HireInSocial\Offers\Application\Offer\EmailFormatter;
use HireInSocial\Offers\Application\Offer\Offers;
use HireInSocial\Offers\Application\System\Calendar;
use HireInSocial\Offers\Application\System\Handler;
use HireInSocial\Offers\Application\System\Mailer;
use HireInSocial\Offers\Application\System\Mailer\Attachments;
use HireInSocial\Offers\Application\System\Mailer\Email;
use HireInSocial\Offers\Application\System\Mailer\Recipient;
use HireInSocial\Offers\Application\System\Mailer\Recipients;
use HireInSocial\Offers\Application\System\Mailer\Sender;
use Ramsey\Uuid\Uuid;

final class ApplyThroughEmailHandler implements Handler
{
    /**
     * @var \HireInSocial\Offers\Application\System\Mailer
     */
    private $mailer;

    /**
     * @var \HireInSocial\Offers\Application\Offer\Offers
     */
    private $offers;

    /**
     * @var \HireInSocial\Offers\Application\Offer\Applications
     */
    private $applications;

    /**
     * @var \HireInSocial\Offers\Application\Hash\Encoder
     */
    private $encoder;

    /**
     * @var \HireInSocial\Offers\Application\System\Calendar
     */
    private $calendar;

    /**
     * @var EmailFormatter
     */
    private $emailFormatter;

    public function __construct(
        Mailer $mailer,
        Offers $offers,
        Applications $applications,
        Encoder $encoder,
        EmailFormatter $emailFormatter,
        Calendar $calendar
    ) {
        $this->mailer = $mailer;
        $this->offers = $offers;
        $this->applications = $applications;
        $this->encoder = $encoder;
        $this->emailFormatter = $emailFormatter;
        $this->calendar = $calendar;
    }

    public function handles() : string
    {
        return ApplyThroughEmail::class;
    }

    public function __invoke(ApplyThroughEmail $command) : void
    {
        $offer = $this->offers->getById(Uuid::fromString($command->offerId()));
        $emailHash = EmailHash::fromRaw($command->from(), $this->encoder);

        if ($this->applications->alreadyApplied($emailHash, $offer)) {
            throw new Exception('This email address already applied for that job offer');
        }

        $this->mailer->send(
            new Email(
                $this->emailFormatter->applicationSubject($command->subject()),
                $this->emailFormatter->applicationBody($command->htmlBody())
            ),
            new Sender(
                \sprintf('no-reply@%s', $this->mailer->domain()),
                $this->mailer->domain(),
                $command->from()
            ),
            new Recipients(new Recipient($offer->contact()->email(), $offer->contact()->name())),
            new Attachments(
                ...\array_map(
                    function (Attachment $attachment) {
                        return new Mailer\Attachment($attachment->filePath());
                    },
                    $command->attachments()
                )
            )
        );

        $this->applications->add(
            Application::forOffer(
                $emailHash,
                $offer,
                $this->calendar
            )
        );
    }
}

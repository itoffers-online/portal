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

namespace ITOffers\Offers\Application\Command\Offer;

use function array_map;
use Aeon\Calendar\Gregorian\Calendar;
use ITOffers\Component\CQRS\System\Handler;
use ITOffers\Component\Mailer\Attachment as EmailAttachment;
use ITOffers\Component\Mailer\Attachments;
use ITOffers\Component\Mailer\Email;
use ITOffers\Component\Mailer\Mailer;
use ITOffers\Component\Mailer\Recipient;
use ITOffers\Component\Mailer\Recipients;
use ITOffers\Component\Mailer\Sender;
use ITOffers\Offers\Application\Command\Offer\Apply\Attachment;
use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Offers\Application\Hash\Encoder;
use ITOffers\Offers\Application\Offer\Application;
use ITOffers\Offers\Application\Offer\Application\EmailHash;
use ITOffers\Offers\Application\Offer\Applications;
use ITOffers\Offers\Application\Offer\EmailFormatter;
use ITOffers\Offers\Application\Offer\Offers;
use Ramsey\Uuid\Uuid;
use function sprintf;

final class ApplyThroughEmailHandler implements Handler
{
    private Mailer $mailer;

    private Offers $offers;

    private Applications $applications;

    private Encoder $encoder;

    private Calendar $calendar;

    private EmailFormatter $emailFormatter;

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
                sprintf('no-reply@%s', $this->mailer->domain()),
                $this->mailer->domain(),
                $command->from()
            ),
            new Recipients(new Recipient($offer->contact()->email(), $offer->contact()->name())),
            new Attachments(
                ...array_map(
                    fn (Attachment $attachment) => new EmailAttachment($attachment->filePath()),
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

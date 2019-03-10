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

namespace HireInSocial\Application\Command\Offer;

use HireInSocial\Application\Command\ClassCommand;
use HireInSocial\Application\Command\Offer\Apply\Attachment;
use HireInSocial\Application\System\Command;

final class ApplyThroughEmail implements Command
{
    use ClassCommand;

    private $offerId;
    private $from;
    private $subject;
    private $htmlBody;
    private $attachments;

    public function __construct(
        string $offerId,
        string $from,
        string $subject,
        string $htmlBody,
        Attachment ...$attachments
    ) {
        $this->offerId = $offerId;
        $this->from = $from;
        $this->subject = $subject;
        $this->htmlBody = $htmlBody;
        $this->attachments = $attachments;
    }

    public function offerId(): string
    {
        return $this->offerId;
    }

    public function from(): string
    {
        return $this->from;
    }

    public function subject(): string
    {
        return $this->subject;
    }

    public function htmlBody(): string
    {
        return $this->htmlBody;
    }

    public function attachments(): array
    {
        return $this->attachments;
    }
}

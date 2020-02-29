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

namespace ITOffers\Component\Mailer;

use ITOffers\Offers\Application\Assertion;

final class Email
{
    private string $subject;

    private string $htmlContent;

    public function __construct(string $subject, string $htmlBody)
    {
        Assertion::notEmpty($subject);
        Assertion::notEmpty($htmlBody);

        $this->subject = $subject;
        $this->htmlContent = $htmlBody;
    }

    public function subject() : string
    {
        return $this->subject;
    }

    public function htmlBody() : string
    {
        return $this->htmlContent;
    }
}

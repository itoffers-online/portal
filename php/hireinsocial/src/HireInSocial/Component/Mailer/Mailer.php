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

namespace HireInSocial\Component\Mailer;

interface Mailer
{
    public function domain() : string;

    public function send(Email $email, Sender $sender, Recipients $recipients, Attachments $attachments) : void;
}

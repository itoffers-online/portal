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

namespace HireInSocial\Application\System\Mailer;

final class Recipient
{
    private $email;

    private $name;

    private $bcc;

    public function __construct(string $email, string $name = null)
    {
        $this->email = $email;
        $this->name = $name;
        $this->bcc = false;
    }

    public static function bcc(string $email, string $name = null)
    {
        $recipient = new self($email, $name);
        $recipient->bcc = true;

        return $recipient;
    }

    public function email() : string
    {
        return $this->email;
    }

    public function name() : ?string
    {
        return $this->name;
    }

    public function isBCC() : bool
    {
        return $this->bcc;
    }
}

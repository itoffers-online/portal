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

final class Recipient
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var bool
     */
    private $bcc;

    public function __construct(string $email, string $name = null)
    {
        $this->email = $email;
        $this->name = $name;
        $this->bcc = false;
    }

    public static function bcc(string $email, string $name = null) : self
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

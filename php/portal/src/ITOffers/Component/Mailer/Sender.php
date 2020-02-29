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

final class Sender
{
    private string $email;

    private string $name;

    private string $replyEmail;

    public function __construct(string $email, string $name, string $replyEmail)
    {
        Assertion::notEmpty($name);
        Assertion::email($email);
        Assertion::email($replyEmail);

        $this->email = $email;
        $this->name = $name;
        $this->replyEmail = $replyEmail;
    }

    public function email() : string
    {
        return $this->email;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function replyEmail() : string
    {
        return $this->replyEmail;
    }
}

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

use HireInSocial\Application\Assertion;

final class Sender
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $replyEmail;

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

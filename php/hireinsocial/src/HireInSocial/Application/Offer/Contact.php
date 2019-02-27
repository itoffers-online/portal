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

namespace HireInSocial\Application\Offer;

use HireInSocial\Application\Assertion;

final class Contact
{
    private $email;
    private $name;
    private $phone;

    public function __construct(string $email, string $name, ?string $phone = null)
    {
        Assertion::email($email);
        Assertion::betweenLength($name, 3, 255);

        if ($phone) {
            Assertion::betweenLength($phone, 6, 16);
        }

        $this->email = $email;
        $this->name = $name;
        $this->phone = $phone;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function phone(): ?string
    {
        return $this->phone;
    }
}

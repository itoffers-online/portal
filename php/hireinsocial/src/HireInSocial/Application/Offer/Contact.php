<?php

declare(strict_types=1);

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

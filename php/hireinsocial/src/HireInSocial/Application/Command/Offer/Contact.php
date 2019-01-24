<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\Offer;

final class Contact
{
    private $email;
    private $name;
    private $phone;

    public function __construct(string $email, string $name, ?string $phone = null)
    {
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

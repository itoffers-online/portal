<?php

declare (strict_types=1);

namespace HireInSocial\Application\Command\Offer;

final class Location
{
    private $remote;
    private $name;

    public function __construct(bool $remote, ?string $name = null)
    {
        $this->remote = $remote;
        $this->name = $name;
    }

    public function remote(): bool
    {
        return $this->remote;
    }

    public function name(): ?string
    {
        return $this->name;
    }
}
<?php

declare(strict_types=1);

namespace HireInSocial\Application\Offer;

use HireInSocial\Application\Assertion;

final class Location
{
    private $remote;
    private $name;

    public function __construct(bool $remote, string $name = null)
    {
        if ($name) {
            Assertion::betweenLength($name, 3, 512);
        }

        $this->remote = $remote;
        $this->name = $name;
    }

    public function isRemote(): bool
    {
        return $this->remote;
    }

    public function name() : ?string
    {
        return $this->name;
    }
}

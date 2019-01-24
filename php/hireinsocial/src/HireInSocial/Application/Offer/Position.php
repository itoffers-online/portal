<?php

declare(strict_types=1);

namespace HireInSocial\Application\Offer;

use HireInSocial\Application\Assertion;

final class Position
{
    private $name;
    private $description;

    public function __construct(string $name, string $description)
    {
        Assertion::betweenLength($name, 3, 255);
        Assertion::betweenLength($description, 50, 1024);

        $this->name = $name;
        $this->description = $description;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }
}

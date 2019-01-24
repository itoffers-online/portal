<?php

declare (strict_types=1);

namespace HireInSocial\Application\Command\Offer;

final class Position
{
    private $name;
    private $description;

    public function __construct(string $name, string $description)
    {
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
<?php

declare (strict_types=1);

namespace HireInSocial\Application\Command\Offer;

final class Company
{
    private $name;
    private $url;
    private $description;

    public function __construct(string $name, string $url, string $description)
    {
        $this->name = $name;
        $this->url = $url;
        $this->description = $description;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function description(): string
    {
        return $this->description;
    }
}
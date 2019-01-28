<?php

declare (strict_types=1);

namespace HireInSocial\Application\Query\Specialization\Model;

final class Specialization
{
    private $slug;
    private $name;

    public function __construct(string $slug, string $name)
    {
        $this->slug = $slug;
        $this->name = $name;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function name(): string
    {
        return $this->name;
    }
}
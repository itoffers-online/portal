<?php

declare (strict_types=1);

namespace HireInSocial\Application\Offer;

use HireInSocial\Application\Assertion;

final class Description
{
    private $requirements;
    private $benefits;

    public function __construct(string $requirements, string $benefits)
    {
        Assertion::betweenLength($requirements, 100, 1024);
        Assertion::betweenLength($benefits, 100, 1024);

        $this->requirements = $requirements;
        $this->benefits = $benefits;
    }

    public function requirements(): string
    {
        return $this->requirements;
    }

    public function benefits(): string
    {
        return $this->benefits;
    }
}
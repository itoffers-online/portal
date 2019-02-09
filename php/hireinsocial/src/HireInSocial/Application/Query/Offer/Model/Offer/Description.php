<?php

declare(strict_types=1);

namespace HireInSocial\Application\Query\Offer\Model\Offer;

final class Description
{
    private $requirements;
    private $benefits;

    public function __construct(string $requirements, string $benefits)
    {
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

<?php

declare(strict_types=1);

namespace HireInSocial\Application\Query\Offer\Model\Offer;

final class Contract
{
    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function type(): string
    {
        return $this->type;
    }
}

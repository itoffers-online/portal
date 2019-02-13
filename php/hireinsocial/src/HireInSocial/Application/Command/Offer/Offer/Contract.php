<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\Offer\Offer;

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

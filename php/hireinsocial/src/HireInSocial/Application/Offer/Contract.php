<?php

declare(strict_types=1);

namespace HireInSocial\Application\Offer;

use HireInSocial\Application\Assertion;

final class Contract
{
    private $type;

    public function __construct(string $type)
    {
        Assertion::notEmpty($type);
        Assertion::betweenLength($type, 3, 255);

        $this->type = $type;
    }

    public function type(): string
    {
        return $this->type;
    }
}

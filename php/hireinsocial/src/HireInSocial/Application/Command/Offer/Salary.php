<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\Offer;

final class Salary
{
    private $min;
    private $max;
    private $currencyCode;
    private $net;

    public function __construct(int $min, int $max, string $currencyCode, bool $net)
    {
        $this->min = $min;
        $this->max = $max;
        $this->currencyCode = $currencyCode;
        $this->net = $net;
    }

    public function min(): int
    {
        return $this->min;
    }

    public function max(): int
    {
        return $this->max;
    }

    public function currencyCode(): string
    {
        return $this->currencyCode;
    }

    public function isNet(): bool
    {
        return $this->net;
    }
}

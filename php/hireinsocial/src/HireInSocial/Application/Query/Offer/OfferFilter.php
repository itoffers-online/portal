<?php

declare (strict_types=1);

namespace HireInSocial\Application\Query\Offer;

use HireInSocial\Application\Query\AbstractFilter;

final class OfferFilter extends AbstractFilter
{
    /**
     * @var string
     */
    private $specialization;

    private function __construct()
    {
    }

    public static function allFor(string $specialization) : self
    {
        $filter = new self();
        $filter->specialization = $specialization;

        return $filter;
    }

    public function specialization(): string
    {
        return $this->specialization;
    }
}
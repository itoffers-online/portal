<?php

declare(strict_types=1);

/*
 * This file is part of the itoffers.online project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ITOffers\Offers\Application\Command\Offer\Offer;

final class Salary
{
    private int $min;

    private int $max;

    private string $currencyCode;

    private bool $net;

    private string $periodType;

    public function __construct(int $min, int $max, string $currencyCode, bool $net, string $periodType)
    {
        $this->min = $min;
        $this->max = $max;
        $this->currencyCode = $currencyCode;
        $this->net = $net;
        $this->periodType = $periodType;
    }

    public function min() : int
    {
        return $this->min;
    }

    public function max() : int
    {
        return $this->max;
    }

    public function currencyCode() : string
    {
        return $this->currencyCode;
    }

    public function isNet() : bool
    {
        return $this->net;
    }

    public function periodType() : string
    {
        return $this->periodType;
    }
}

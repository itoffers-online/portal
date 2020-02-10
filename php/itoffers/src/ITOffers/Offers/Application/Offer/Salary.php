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

namespace ITOffers\Offers\Application\Offer;

use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\Offer\Salary\Period;

final class Salary
{
    /**
     * @var int
     */
    private $min;

    /**
     * @var int
     */
    private $max;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var bool
     */
    private $net;

    /**
     * @var Period
     */
    private $period;

    public function __construct(int $min, int $max, string $currencyCode, bool $net, Period $period)
    {
        Assertion::greaterThan($min, 0);
        Assertion::greaterOrEqualThan($max, $min);
        Assertion::length($currencyCode, 3);

        $this->min = $min;
        $this->max = $max;
        $this->currencyCode = $currencyCode;
        $this->net = $net;
        $this->period = $period;
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

    public function period() : Period
    {
        return $this->period;
    }
}

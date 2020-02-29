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

namespace ITOffers\Offers\Application\Query\Offer\Model\Offer;

final class Salary
{
    public const PERIOD_TYPE_HOUR = 'HOUR';

    public const PERIOD_TYPE_DAY = 'DAY';

    public const PERIOD_TYPE_WEEK = 'WEEK';

    public const PERIOD_TYPE_MONTH = 'MONTH';

    public const PERIOD_TYPE_YEAR = 'YEAR';

    public const PERIOD_TYPE_IN_TOTAL = 'IN_TOTAL';

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
        return \mb_strtoupper($this->currencyCode);
    }

    public function isNet() : bool
    {
        return $this->net;
    }

    public function periodType() : string
    {
        return \mb_strtolower($this->periodType);
    }

    public function periodTypeTotal() : bool
    {
        return $this->periodType === self::PERIOD_TYPE_IN_TOTAL;
    }
}

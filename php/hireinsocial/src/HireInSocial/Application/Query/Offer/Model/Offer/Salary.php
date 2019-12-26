<?php

declare(strict_types=1);

/*
 * This file is part of the Hire in Social project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HireInSocial\Application\Query\Offer\Model\Offer;

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
}

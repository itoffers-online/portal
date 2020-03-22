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

namespace ITOffers\Tests\Offers\Application\MotherObject\Offer;

use ITOffers\Offers\Application\Offer\Salary;
use ITOffers\Offers\Application\Offer\Salary\Period;

final class SalaryMother
{
    public static function random() : Salary
    {
        return new Salary(\random_int(1_000, 5_000), \random_int(5_001, 10_000), 'PLN', true, Period::perMonth());
    }

    public static function netPLN(int $min, int $max) : Salary
    {
        return new Salary($min, $max, 'PLN', true, Period::perMonth());
    }
}

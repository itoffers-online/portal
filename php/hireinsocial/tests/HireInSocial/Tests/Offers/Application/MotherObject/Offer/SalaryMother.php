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

namespace HireInSocial\Tests\Offers\Application\MotherObject\Offer;

use HireInSocial\Offers\Application\Offer\Salary;

final class SalaryMother
{
    public static function netPLN(int $min, int $max) : Salary
    {
        return new Salary($min, $max, 'PLN', true, Salary\Period::perMonth());
    }
}

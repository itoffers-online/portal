<?php

declare (strict_types=1);

namespace HireInSocial\Tests\Application\MotherObject\Offer;

use HireInSocial\Application\Offer\Salary;

final class SalaryMother
{
    public static function netPLN(int $min, int $max) : Salary
    {
        return new Salary($min, $max, 'PLN', true);
    }
}
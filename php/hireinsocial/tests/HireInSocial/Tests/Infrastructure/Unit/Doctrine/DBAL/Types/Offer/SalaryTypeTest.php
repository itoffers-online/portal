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

namespace HireInSocial\Tests\Infrastructure\Unit\Doctrine\DBAL\Types\Offer;

use HireInSocial\Application\Offer\Salary;
use HireInSocial\Infrastructure\Doctrine\DBAL\Types\Offer\SalaryType;
use HireInSocial\Tests\Application\MotherObject\Offer\SalaryMother;
use HireInSocial\Tests\Infrastructure\Unit\Doctrine\DBAL\Types\TypeTestCase;

final class SalaryTypeTest extends TypeTestCase
{
    protected function getTypeName(): string
    {
        return SalaryType::NAME;
    }

    protected function getTypeClass(): string
    {
        return SalaryType::class;
    }

    /**
     * @return array<array>
     */
    public function dataProvider() : array
    {
        return [
            [null],
            [SalaryMother::netPLN(1000, 5000)],
        ];
    }
}

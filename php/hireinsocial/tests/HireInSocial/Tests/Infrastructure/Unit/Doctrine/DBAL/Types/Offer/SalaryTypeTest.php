<?php

declare (strict_types=1);

namespace HireInSocial\Tests\Infrastructure\Unit\Doctrine\DBAL\Types\Offer;

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

    public function dataProvider() : array
    {
        return [
            [null],
            [SalaryMother::netPLN(1000, 5000)]
        ];
    }
}
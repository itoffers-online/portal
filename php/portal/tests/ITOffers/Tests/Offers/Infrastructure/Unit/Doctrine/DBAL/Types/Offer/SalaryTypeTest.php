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

namespace ITOffers\Tests\Offers\Infrastructure\Unit\Doctrine\DBAL\Types\Offer;

use Iterator;
use ITOffers\Offers\Infrastructure\Doctrine\DBAL\Types\Offer\SalaryType;
use ITOffers\Tests\Offers\Application\MotherObject\Offer\SalaryMother;
use ITOffers\Tests\Offers\Infrastructure\Unit\Doctrine\DBAL\Types\TypeTestCase;

final class SalaryTypeTest extends TypeTestCase
{
    protected function getTypeName() : string
    {
        return SalaryType::NAME;
    }

    protected function getTypeClass() : string
    {
        return SalaryType::class;
    }

    public function dataProvider() : Iterator
    {
        yield [null];
        yield [SalaryMother::netPLN(1_000, 5_000)];
    }
}

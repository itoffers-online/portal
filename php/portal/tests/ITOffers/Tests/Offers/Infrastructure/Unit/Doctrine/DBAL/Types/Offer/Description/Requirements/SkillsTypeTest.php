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

namespace ITOffers\Tests\Offers\Infrastructure\Unit\Doctrine\DBAL\Types\Offer\Description\Requirements;

use ITOffers\Offers\Application\Offer\Description\Requirements\Skill;
use ITOffers\Offers\Infrastructure\Doctrine\DBAL\Types\Offer\Description\Requirements\SkillsType;
use ITOffers\Tests\Offers\Infrastructure\Unit\Doctrine\DBAL\Types\TypeTestCase;

final class SkillsTypeTest extends TypeTestCase
{
    protected function getTypeName() : string
    {
        return SkillsType::NAME;
    }

    protected function getTypeClass() : string
    {
        return SkillsType::class;
    }

    public function dataProvider() : \Iterator
    {
        yield [
            [],
        ];
        yield [
            [
                new Skill('php', true, 5),
                new Skill('java', false),
                new Skill('go', true),
            ],
        ];
        yield [
            null,
        ];
    }
}

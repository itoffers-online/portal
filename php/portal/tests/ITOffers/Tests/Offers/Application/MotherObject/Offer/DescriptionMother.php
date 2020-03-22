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

use Faker\Factory;
use ITOffers\Offers\Application\Offer\Description;
use ITOffers\Offers\Application\Offer\Description\Requirements;
use ITOffers\Offers\Application\Offer\Description\Requirements\Skill;

final class DescriptionMother
{
    public static function random() : Description
    {
        $faker = Factory::create();

        return new Description(
            $faker->text(2_048),
            $faker->text(1_024),
            new Requirements(
                $faker->text(2_048),
                new Skill(
                    'php',
                    true,
                    10
                )
            ),
        );
    }
}

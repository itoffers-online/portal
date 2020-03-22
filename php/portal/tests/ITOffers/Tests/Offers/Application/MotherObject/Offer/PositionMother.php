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
use ITOffers\Offers\Application\Offer\Position;

final class PositionMother
{
    public static function random() : Position
    {
        $faker = Factory::create();

        return new Position(
            \random_int(0, 4),
            $faker->jobTitle
        );
    }
}

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
use ITOffers\Offers\Application\Offer\Company;

final class CompanyMother
{
    public static function random() : Company
    {
        $faker = Factory::create();

        return new Company($faker->company, $faker->url, $faker->text(512));
    }
}

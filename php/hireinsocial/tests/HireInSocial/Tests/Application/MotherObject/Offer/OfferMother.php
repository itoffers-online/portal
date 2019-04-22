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

namespace HireInSocial\Tests\Application\MotherObject\Offer;

use Faker\Factory;
use HireInSocial\Application\Offer;
use HireInSocial\Tests\Application\MotherObject\Facebook\CalendarMother;
use HireInSocial\Tests\Application\MotherObject\Specialization\SpecializationMother;
use HireInSocial\Tests\Application\MotherObject\User\UserMother;

final class OfferMother
{
    public static function random() : Offer\Offer
    {
        return self::withName('position', 'company');
    }

    public static function withName(string $positionName, string $companyName) : Offer\Offer
    {
        $faker = Factory::create();

        return Offer\Offer::postIn(
            SpecializationMother::random(),
            UserMother::random(),
            new Offer\Company($companyName, $faker->url, $faker->text(512)),
            new Offer\Position($positionName, $faker->text(1024)),
            Offer\Location::onlyRemote(),
            new Offer\Salary($faker->numberBetween(1000, 5000), $faker->numberBetween(5000, 20000), 'PLN', $faker->boolean),
            new Offer\Contract('B2B'),
            new Offer\Description(
                $faker->text(1024),
                $faker->text(1024)
            ),
            new Offer\Contact(
                $faker->email,
                $faker->name,
                '+1 333333333'
            ),
            CalendarMother::utc()
        );
    }
}

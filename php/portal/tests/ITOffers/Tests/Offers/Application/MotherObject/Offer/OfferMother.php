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
use ITOffers\Offers\Application\Offer;
use ITOffers\Offers\Application\User\User;
use ITOffers\Tests\Offers\Application\MotherObject\Facebook\CalendarMother;
use ITOffers\Tests\Offers\Application\MotherObject\Specialization\SpecializationMother;
use ITOffers\Tests\Offers\Application\MotherObject\User\UserMother;
use Ramsey\Uuid\Uuid;

final class OfferMother
{
    public static function random() : Offer\Offer
    {
        return self::withName('position', 'company');
    }

    public static function byUser(User $user) : Offer\Offer
    {
        return self::withName('position', 'company', $user);
    }

    public static function withName(string $positionName, string $companyName, ?User $user = null) : Offer\Offer
    {
        $faker = Factory::create();

        return Offer\Offer::post(
            Uuid::uuid4(),
            SpecializationMother::random(),
            new Offer\Locale('en_US'),
            $user ? $user : UserMother::random(),
            new Offer\Company($companyName, $faker->url, $faker->text(512)),
            new Offer\Position(\random_int(Offer\Position\SeniorityLevels::INTERN, Offer\Position\SeniorityLevels::EXPERT), $positionName, $faker->text(1024)),
            Offer\Location::remote(),
            new Offer\Salary($faker->numberBetween(1000, 5000), $faker->numberBetween(5000, 20000), 'PLN', $faker->boolean, Offer\Salary\Period::perMonth()),
            new Offer\Contract('B2B'),
            new Offer\Description(
                $faker->text(1024),
                new Offer\Description\Requirements(
                    $faker->text(2048),
                    new Offer\Description\Requirements\Skill(
                        'php',
                        true,
                        10
                    )
                ),
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

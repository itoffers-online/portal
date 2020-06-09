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

use Aeon\Calendar\Gregorian\Calendar;
use Aeon\Calendar\Gregorian\GregorianCalendarStub;
use Faker\Factory;
use ITOffers\Offers\Application\Offer\Company;
use ITOffers\Offers\Application\Offer\Contact;
use ITOffers\Offers\Application\Offer\Contract;
use ITOffers\Offers\Application\Offer\Description;
use ITOffers\Offers\Application\Offer\Description\Requirements;
use ITOffers\Offers\Application\Offer\Description\Requirements\Skill;
use ITOffers\Offers\Application\Offer\Locale;
use ITOffers\Offers\Application\Offer\Location;
use ITOffers\Offers\Application\Offer\Offer;
use ITOffers\Offers\Application\Offer\Position;
use ITOffers\Offers\Application\Offer\Position\SeniorityLevels;
use ITOffers\Offers\Application\Offer\Salary;
use ITOffers\Offers\Application\Offer\Salary\Period;
use ITOffers\Offers\Application\User\User;
use ITOffers\Tests\Offers\Application\MotherObject\Specialization\SpecializationMother;
use ITOffers\Tests\Offers\Application\MotherObject\User\UserMother;
use Ramsey\Uuid\Uuid;

final class OfferMother
{
    public static function random() : Offer
    {
        return self::withName('position', 'company');
    }

    public static function byUser(User $user, ?Calendar $calendar = null) : Offer
    {
        return self::withName('position', 'company', $user, $calendar);
    }

    public static function withName(string $positionName, string $companyName, ?User $user = null, ?Calendar $calendar = null) : Offer
    {
        $faker = Factory::create();

        return Offer::post(
            Uuid::uuid4(),
            SpecializationMother::random(),
            new Locale('en_US'),
            $user ? $user : UserMother::random(),
            new Company($companyName, $faker->url, $faker->text(512)),
            new Position(\random_int(SeniorityLevels::INTERN, SeniorityLevels::EXPERT), $positionName),
            Location::remote(),
            new Salary($faker->numberBetween(1_000, 5_000), $faker->numberBetween(5_000, 20_000), 'PLN', $faker->boolean, Period::perMonth()),
            new Contract('B2B'),
            new Description(
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
            ),
            Contact::recruiter(
                $faker->email,
                $faker->name,
                '+1 333333333'
            ),
            $calendar ? $calendar : new GregorianCalendarStub()
        );
    }
}

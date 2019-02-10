<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\MotherObject\Offer;

use Faker\Factory;
use HireInSocial\Application\Offer;
use HireInSocial\Tests\Application\MotherObject\Facebook\CalendarMother;
use Ramsey\Uuid\Uuid;

final class OfferMother
{
    public static function withName(string $positionName, string $companyName)
    {
        $faker = Factory::create();

        return new Offer\Offer(
            Uuid::uuid4(),
            new Offer\Company($companyName, $faker->url, $faker->text(512)),
            new Offer\Position($positionName, $faker->text(1024)),
            new Offer\Location($faker->boolean, $faker->country),
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

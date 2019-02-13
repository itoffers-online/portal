<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\MotherObject\Command\Offer;

use Faker\Factory;
use HireInSocial\Application\Command\Offer\PostOffer;
use HireInSocial\Application\Command\Offer\Offer\Channels;
use HireInSocial\Application\Command\Offer\Offer\Company;
use HireInSocial\Application\Command\Offer\Offer\Contact;
use HireInSocial\Application\Command\Offer\Offer\Contract;
use HireInSocial\Application\Command\Offer\Offer\Description;
use HireInSocial\Application\Command\Offer\Offer\Location;
use HireInSocial\Application\Command\Offer\Offer\Offer;
use HireInSocial\Application\Command\Offer\Offer\Position;
use HireInSocial\Application\Command\Offer\Offer\Salary;

final class PostOfferMother
{
    public static function postAs(string $fbUserId, string $specialization) : \HireInSocial\Application\Command\Offer\PostOffer
    {
        $faker = Factory::create();

        return new \HireInSocial\Application\Command\Offer\PostOffer(
            $specialization,
            $fbUserId,
            new Offer(
                new Company($faker->company, $faker->url, $faker->text(512)),
                new Position('PHP Developer', $faker->text(1024)),
                new Location($faker->boolean, $faker->country),
                new Salary($faker->numberBetween(1000, 5000), $faker->numberBetween(5000, 20000), 'PLN', $faker->boolean),
                new Contract('B2B'),
                new Description(
                    $faker->text(1024),
                    $faker->text(1024)
                ),
                new Contact(
                    $faker->email,
                    $faker->name,
                    '+1 333333333'
                ),
                new Channels(
                    true
                )
            )
        );
    }

    public static function postAsOnFB(string $fbUserId, string $specialization) : \HireInSocial\Application\Command\Offer\PostOffer
    {
        $faker = Factory::create();

        return new PostOffer(
            $specialization,
            $fbUserId,
            new Offer(
                new Company($faker->company, $faker->url, $faker->text(512)),
                new Position('PHP Developer', $faker->text(1024)),
                new Location($faker->boolean, $faker->country),
                new Salary($faker->numberBetween(1000, 5000), $faker->numberBetween(5000, 20000), 'PLN', $faker->boolean),
                new Contract('B2B'),
                new Description(
                    $faker->text(1024),
                    $faker->text(1024)
                ),
                new Contact(
                    $faker->email,
                    $faker->name,
                    '+1 333333333'
                ),
                new Channels(
                    true
                )
            )
        );
    }
}

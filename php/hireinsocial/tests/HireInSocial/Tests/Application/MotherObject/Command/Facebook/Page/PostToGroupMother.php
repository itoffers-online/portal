<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\MotherObject\Command\Facebook\Page;

use Faker\Factory;
use HireInSocial\Application\Command\Facebook\Page\PostToGroup;
use HireInSocial\Application\Command\Offer\Company;
use HireInSocial\Application\Command\Offer\Contact;
use HireInSocial\Application\Command\Offer\Contract;
use HireInSocial\Application\Command\Offer\Description;
use HireInSocial\Application\Command\Offer\Location;
use HireInSocial\Application\Command\Offer\Offer;
use HireInSocial\Application\Command\Offer\Position;
use HireInSocial\Application\Command\Offer\Salary;

final class PostToGroupMother
{
    public static function postAs(string $fbUserId, string $specialization) : PostToGroup
    {
        $faker = Factory::create();

        return new PostToGroup(
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
                )
            )
        );
    }
}

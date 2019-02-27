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

namespace HireInSocial\Tests\Application\MotherObject\Command\Offer;

use Faker\Factory;
use HireInSocial\Application\Command\Offer\Offer\Channels;
use HireInSocial\Application\Command\Offer\Offer\Company;
use HireInSocial\Application\Command\Offer\Offer\Contact;
use HireInSocial\Application\Command\Offer\Offer\Contract;
use HireInSocial\Application\Command\Offer\Offer\Description;
use HireInSocial\Application\Command\Offer\Offer\Location;
use HireInSocial\Application\Command\Offer\Offer\Offer;
use HireInSocial\Application\Command\Offer\Offer\Position;
use HireInSocial\Application\Command\Offer\Offer\Salary;
use HireInSocial\Application\Command\Offer\PostOffer;

final class PostOfferMother
{
    public static function random(string $userId, string $specialization) : PostOffer
    {
        $faker = Factory::create();

        return new PostOffer(
            $specialization,
            $userId,
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
                    false
                )
            )
        );
    }

    public static function withoutSalary(string $userId, string $specialization) : PostOffer
    {
        $faker = Factory::create();

        return new PostOffer(
            $specialization,
            $userId,
            new Offer(
                new Company($faker->company, $faker->url, $faker->text(512)),
                new Position('PHP Developer', $faker->text(1024)),
                new Location($faker->boolean, $faker->country),
                null,
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
                    false
                )
            )
        );
    }

    public static function withSalary(string $userId, string $specialization, int $min, int $max, string $currency = 'USD') : PostOffer
    {
        $faker = Factory::create();

        return new PostOffer(
            $specialization,
            $userId,
            new Offer(
                new Company($faker->company, $faker->url, $faker->text(512)),
                new Position('PHP Developer', $faker->text(1024)),
                new Location($faker->boolean, $faker->country),
                new Salary($min, $max, $currency, $faker->boolean),
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
                    false
                )
            )
        );
    }

    public static function notRemote(string $userId, string $specialization) : PostOffer
    {
        $faker = Factory::create();

        return new PostOffer(
            $specialization,
            $userId,
            new Offer(
                new Company($faker->company, $faker->url, $faker->text(512)),
                new Position('PHP Developer', $faker->text(1024)),
                new Location(false, $faker->country),
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
                    false
                )
            )
        );
    }

    public static function remote(string $userId, string $specialization) : PostOffer
    {
        $faker = Factory::create();

        return new PostOffer(
            $specialization,
            $userId,
            new Offer(
                new Company($faker->company, $faker->url, $faker->text(512)),
                new Position('PHP Developer', $faker->text(1024)),
                new Location(true, $faker->country),
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
                    false
                )
            )
        );
    }

    public static function onFB(string $fbUserId, string $specialization) : PostOffer
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

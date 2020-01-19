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

namespace HireInSocial\Tests\Offers\Application\MotherObject\Command\Offer;

use Faker\Factory;
use HireInSocial\Offers\Application\Command\Offer\Offer\Channels;
use HireInSocial\Offers\Application\Command\Offer\Offer\Company;
use HireInSocial\Offers\Application\Command\Offer\Offer\Contact;
use HireInSocial\Offers\Application\Command\Offer\Offer\Contract;
use HireInSocial\Offers\Application\Command\Offer\Offer\Description;
use HireInSocial\Offers\Application\Command\Offer\Offer\Location;
use HireInSocial\Offers\Application\Command\Offer\Offer\Offer;
use HireInSocial\Offers\Application\Command\Offer\Offer\Position;
use HireInSocial\Offers\Application\Command\Offer\Offer\Salary;
use HireInSocial\Offers\Application\Command\Offer\PostOffer;
use HireInSocial\Offers\Application\Offer\Position\SeniorityLevels;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Salary as SalaryView;

final class PostOfferMother
{
    public static function random(string $offerId, string $userId, string $specialization) : PostOffer
    {
        $faker = Factory::create();

        return new PostOffer(
            $offerId,
            $specialization,
            'en_US',
            $userId,
            new Offer(
                new Company($faker->company, $faker->url, $faker->text(512)),
                new Position(\random_int(SeniorityLevels::INTERN, SeniorityLevels::EXPERT), 'PHP Developer', $faker->text(1024)),
                new Location($faker->boolean, $faker->countryCode, $faker->city, new Location\LatLng($faker->latitude, $faker->longitude)),
                new Salary($faker->numberBetween(1000, 5000), $faker->numberBetween(5000, 20000), 'PLN', $faker->boolean, SalaryView::PERIOD_TYPE_MONTH),
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

    public static function randomWithPDF(string $offerId, string $userId, string $specialization, string $offerPDFPath) : PostOffer
    {
        $faker = Factory::create();

        return new PostOffer(
            $offerId,
            $specialization,
            'en_US',
            $userId,
            new Offer(
                new Company($faker->company, $faker->url, $faker->text(512)),
                new Position(\random_int(SeniorityLevels::INTERN, SeniorityLevels::EXPERT), 'PHP Developer', $faker->text(1024)),
                new Location($faker->boolean, $faker->countryCode, $faker->city, new Location\LatLng($faker->latitude, $faker->longitude)),
                new Salary($faker->numberBetween(1000, 5000), $faker->numberBetween(5000, 20000), 'PLN', $faker->boolean, SalaryView::PERIOD_TYPE_MONTH),
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
            ),
            $offerPDFPath
        );
    }

    public static function withoutSalary(string $offerId, string $userId, string $specialization) : PostOffer
    {
        $faker = Factory::create();

        return new PostOffer(
            $offerId,
            $specialization,
            'en_US',
            $userId,
            new Offer(
                new Company($faker->company, $faker->url, $faker->text(512)),
                new Position(\random_int(SeniorityLevels::INTERN, SeniorityLevels::EXPERT), 'PHP Developer', $faker->text(1024)),
                new Location($faker->boolean, $faker->countryCode, $faker->city, new Location\LatLng($faker->latitude, $faker->longitude)),
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

    public static function withSalary(string $offerId, string $userId, string $specialization, int $min, int $max, string $currency = 'USD') : PostOffer
    {
        $faker = Factory::create();

        return new PostOffer(
            $offerId,
            $specialization,
            'en_US',
            $userId,
            new Offer(
                new Company($faker->company, $faker->url, $faker->text(512)),
                new Position(\random_int(SeniorityLevels::INTERN, SeniorityLevels::EXPERT), 'PHP Developer', $faker->text(1024)),
                new Location($faker->boolean, $faker->countryCode, $faker->city, new Location\LatLng($faker->latitude, $faker->longitude)),
                new Salary($min, $max, $currency, $faker->boolean, SalaryView::PERIOD_TYPE_MONTH),
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

    public static function notRemote(string $offerId, string $userId, string $specialization) : PostOffer
    {
        $faker = Factory::create();

        return new PostOffer(
            $offerId,
            $specialization,
            'en_US',
            $userId,
            new Offer(
                new Company($faker->company, $faker->url, $faker->text(512)),
                new Position(\random_int(SeniorityLevels::INTERN, SeniorityLevels::EXPERT), 'PHP Developer', $faker->text(1024)),
                new Location(false, $faker->countryCode, $faker->city, new Location\LatLng($faker->latitude, $faker->longitude)),
                new Salary($faker->numberBetween(1000, 5000), $faker->numberBetween(5000, 20000), 'PLN', $faker->boolean, SalaryView::PERIOD_TYPE_MONTH),
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

    public static function remote(string $offerId, string $userId, string $specialization) : PostOffer
    {
        $faker = Factory::create();

        return new PostOffer(
            $offerId,
            $specialization,
            'en_US',
            $userId,
            new Offer(
                new Company($faker->company, $faker->url, $faker->text(512)),
                new Position(\random_int(SeniorityLevels::INTERN, SeniorityLevels::EXPERT), 'PHP Developer', $faker->text(1024)),
                new Location(true),
                new Salary($faker->numberBetween(1000, 5000), $faker->numberBetween(5000, 20000), 'PLN', $faker->boolean, SalaryView::PERIOD_TYPE_MONTH),
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

    public static function onFB(string $offerId, string $fbUserId, string $specialization) : PostOffer
    {
        $faker = Factory::create();

        return new PostOffer(
            $offerId,
            $specialization,
            'en_US',
            $fbUserId,
            new Offer(
                new Company($faker->company, $faker->url, $faker->text(512)),
                new Position(\random_int(SeniorityLevels::INTERN, SeniorityLevels::EXPERT), 'PHP Developer', $faker->text(1024)),
                new Location($faker->boolean, $faker->countryCode, $faker->city, new Location\LatLng($faker->latitude, $faker->longitude)),
                new Salary($faker->numberBetween(1000, 5000), $faker->numberBetween(5000, 20000), 'PLN', $faker->boolean, SalaryView::PERIOD_TYPE_MONTH),
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

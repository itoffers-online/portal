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

namespace ITOffers\Tests\Offers\Application\Unit\Offer;

use Aeon\Calendar\Gregorian\DateTime;
use Aeon\Calendar\Gregorian\GregorianCalendarStub;
use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Offers\Application\Offer\Locale;
use ITOffers\Tests\Offers\Application\MotherObject\Offer\CompanyMother;
use ITOffers\Tests\Offers\Application\MotherObject\Offer\ContactMother;
use ITOffers\Tests\Offers\Application\MotherObject\Offer\ContractMother;
use ITOffers\Tests\Offers\Application\MotherObject\Offer\DescriptionMother;
use ITOffers\Tests\Offers\Application\MotherObject\Offer\LocationMother;
use ITOffers\Tests\Offers\Application\MotherObject\Offer\OfferMother;
use ITOffers\Tests\Offers\Application\MotherObject\Offer\PositionMother;
use ITOffers\Tests\Offers\Application\MotherObject\Offer\SalaryMother;
use ITOffers\Tests\Offers\Application\MotherObject\User\UserMother;
use PHPUnit\Framework\TestCase;

final class OfferTest extends TestCase
{
    public function test_update_offer_not_by_author() : void
    {
        $offer = OfferMother::random();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User is not allowed to update the offer');

        $offer->update(
            UserMother::random(),
            new Locale('pl_PL'),
            CompanyMother::random(),
            PositionMother::random(),
            LocationMother::remote(),
            SalaryMother::random(),
            ContractMother::random(),
            DescriptionMother::random(),
            ContactMother::random(),
            new GregorianCalendarStub()
        );
    }

    public function test_update_offer_after_allowed_time() : void
    {
        $calendar = new GregorianCalendarStub();
        $offer = OfferMother::byUser($user = UserMother::random(), $calendar);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('This offer can\'t be updated anymore');

        $calendar->setNow(DateTime::fromString('+5 hours'));

        $offer->update(
            $user,
            new Locale('pl_PL'),
            CompanyMother::random(),
            PositionMother::random(),
            LocationMother::remote(),
            SalaryMother::random(),
            ContractMother::random(),
            DescriptionMother::random(),
            ContactMother::random(),
            $calendar
        );
    }
}

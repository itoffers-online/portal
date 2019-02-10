<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Unit\Offer;

use HireInSocial\Application\Offer\Slug;
use HireInSocial\Tests\Application\MotherObject\Facebook\CalendarMother;
use HireInSocial\Tests\Application\MotherObject\Offer\OfferMother;
use PHPStan\Testing\TestCase;

final class SlugTest extends TestCase
{
    public function test_creating_offer_slug()
    {
        $order = OfferMother::withName('Senior PHP Developer', 'Super Company');

        $this->assertRegExp(
            '/^senior-php-developer-super-company-(.)+/',
            (string) Slug::from($order, CalendarMother::utc())
        );
    }
}

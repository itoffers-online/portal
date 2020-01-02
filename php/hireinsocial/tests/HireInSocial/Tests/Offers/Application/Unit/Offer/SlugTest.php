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

namespace HireInSocial\Tests\Offers\Application\Unit\Offer;

use HireInSocial\Offers\Application\Offer\Slug;
use HireInSocial\Tests\Offers\Application\MotherObject\Facebook\CalendarMother;
use HireInSocial\Tests\Offers\Application\MotherObject\Offer\OfferMother;
use PHPStan\Testing\TestCase;

final class SlugTest extends TestCase
{
    public function test_creating_offer_slug() : void
    {
        $order = OfferMother::withName('PHP Developer', 'Super Company');

        $this->assertRegExp(
            '/^(intern|junior|mid|senior|expert)-php-developer-super-company-(.)+/',
            (string) Slug::from($order, CalendarMother::utc())
        );
    }
}

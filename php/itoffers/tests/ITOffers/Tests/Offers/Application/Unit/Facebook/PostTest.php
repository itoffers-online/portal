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

namespace ITOffers\Tests\Offers\Application\Unit\Facebook;

use ITOffers\Offers\Application\Exception\InvalidAssertionException;
use ITOffers\Offers\Application\Facebook\Post;
use ITOffers\Tests\Offers\Application\MotherObject\Offer\OfferMother;
use PHPUnit\Framework\TestCase;

final class PostTest extends TestCase
{
    public function test_create_page_with_too_long_fb_id() : void
    {
        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Invalid FB Post ID');

        new Post(\str_repeat('1', 256), OfferMother::random());
    }

    public function test_create_page_with_too_short_fb_id() : void
    {
        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Invalid FB Post ID');

        new Post('1', OfferMother::random());
    }
}

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

namespace HireInSocial\Tests\Application\Unit\Facebook;

use HireInSocial\Application\Exception\InvalidAssertionException;
use HireInSocial\Application\Facebook\Post;
use HireInSocial\Tests\Application\MotherObject\Offer\OfferMother;
use PHPUnit\Framework\TestCase;

final class PostTest extends TestCase
{
    public function test_create_page_with_too_long_fb_id()
    {
        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Invalid FB Post ID');

        new Post(\str_repeat('1', 256), OfferMother::random());
    }

    public function test_create_page_with_too_short_fb_id()
    {
        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Invalid FB Post ID');

        new Post('1', OfferMother::random());
    }
}

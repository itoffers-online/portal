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
use HireInSocial\Application\Facebook\Page;
use PHPUnit\Framework\TestCase;

final class PageTest extends TestCase
{
    public function test_create_page_with_too_long_fb_id() : void
    {
        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Invalid FB Page ID');

        new Page(\str_repeat('1', 256), 'access_token');
    }

    public function test_create_page_with_too_short_fb_id() : void
    {
        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Invalid FB Page ID');

        new Page('1', 'access_token');
    }

    public function test_create_page_with_too_long_fb_token() : void
    {
        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Invalid FB Page Token');

        new Page('fb_page_id', \str_repeat('1', 256));
    }

    public function test_create_page_with_too_short_fb_token() : void
    {
        $this->expectException(InvalidAssertionException::class);
        $this->expectExceptionMessage('Invalid FB Page Token');

        new Page('fb_page_id', '1');
    }
}

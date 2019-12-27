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

namespace HireInSocial\Tests\Offers\Application\MotherObject\Facebook;

use HireInSocial\Offers\Application\Facebook\Page;

final class PageMother
{
    public static function random() : Page
    {
        return new Page('1234567890', 'access_token');
    }
}

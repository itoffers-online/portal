<?php

declare (strict_types=1);

namespace HireInSocial\Tests\Application\MotherObject\Facebook;

use HireInSocial\Application\Facebook\Page;

final class PageMother
{
    public static function random() : Page
    {
        return new Page('1234567890', 'access_token');
    }
}
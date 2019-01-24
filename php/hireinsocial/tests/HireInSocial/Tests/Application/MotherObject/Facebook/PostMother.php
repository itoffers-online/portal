<?php

declare (strict_types=1);

namespace HireInSocial\Tests\Application\MotherObject\Facebook;

use HireInSocial\Application\Facebook\Draft;

final class PostMother
{
    public static function random() : Draft
    {
        return self::withAuthor('1234567890');
    }

    public static function withAuthor(string $authorFbId) : Draft
    {
        return new Draft($authorFbId, 'random post message', 'http://test.com');
    }
}
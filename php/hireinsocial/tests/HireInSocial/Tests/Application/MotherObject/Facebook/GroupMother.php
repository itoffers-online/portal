<?php

declare (strict_types=1);

namespace HireInSocial\Tests\Application\MotherObject\Facebook;

use HireInSocial\Application\Facebook\Group;

final class GroupMother
{
    public static function random() : Group
    {
        return new Group('123456789');
    }
}
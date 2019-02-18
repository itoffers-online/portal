<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\MotherObject\User;

use HireInSocial\Application\User\User;
use HireInSocial\Common\PrivateFields;
use HireInSocial\Tests\Application\MotherObject\Facebook\CalendarMother;
use Ramsey\Uuid\UuidInterface;

final class UserMother
{
    use PrivateFields;

    public static function withId(UuidInterface $id) : User
    {
        $user = User::fromFacebook('facebook_id', CalendarMother::utc());
        self::setPrivatePropertyValue($user, 'id', $id);

        return $user;
    }

    public static function random() : User
    {
        return User::fromFacebook('facebook_id', CalendarMother::utc());
    }
}

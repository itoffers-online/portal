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

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

namespace ITOffers\Tests\Offers\Application\MotherObject\User;

use Aeon\Calendar\Gregorian\GregorianCalendarStub;
use ITOffers\Component\Reflection\PrivateFields;
use ITOffers\Offers\Application\User\User;
use Ramsey\Uuid\UuidInterface;

final class UserMother
{
    use PrivateFields;

    public static function withId(UuidInterface $id) : User
    {
        $user = User::fromFacebook('facebook_id', 'user@itoffers.online', new GregorianCalendarStub());
        self::setPrivatePropertyValue($user, 'id', $id);

        return $user;
    }

    public static function random() : User
    {
        return User::fromFacebook('facebook_id', 'user@itoffers.online', new GregorianCalendarStub());
    }
}

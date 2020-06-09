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

namespace ITOffers\Offers\Application\User;

use Aeon\Calendar\Gregorian\Calendar;
use Aeon\Calendar\Gregorian\DateTime;
use ITOffers\Offers\Application\Assertion;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class User
{
    private string $id;

    private string $email;

    private DateTime $createdAt;

    private ?string $fbUserAppId = null;

    private ?string $linkedInUserAppId = null;

    private ?DateTime $blockedAt = null;

    private function __construct(DateTime $createdAt, string $email)
    {
        Assertion::email($email);

        $this->id = Uuid::uuid4()->toString();
        $this->createdAt = $createdAt;
        $this->email = \mb_strtolower($email);
    }

    public static function fromFacebook(string $userAppId, string $email, Calendar $calendar) : self
    {
        Assertion::betweenLength($userAppId, 0, 255);

        $user = new self($calendar->now(), $email);
        $user->fbUserAppId = $userAppId;

        return $user;
    }

    public static function fromLinkedIn(string $userAppId, string $email, Calendar $calendar) : self
    {
        Assertion::betweenLength($userAppId, 0, 255);

        $user = new self($calendar->now(), $email);
        $user->linkedInUserAppId = $userAppId;

        return $user;
    }

    public function id() : UuidInterface
    {
        return Uuid::fromString($this->id);
    }

    public function block(Calendar $calendar) : void
    {
        $this->blockedAt = $calendar->now();
    }

    public function email() : string
    {
        return $this->email;
    }
}

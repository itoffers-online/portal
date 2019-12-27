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

namespace HireInSocial\Application\User;

use HireInSocial\Application\Assertion;
use HireInSocial\Application\System\Calendar;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class User
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $fbUserAppId;

    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    private function __construct(\DateTimeImmutable $createdAt)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->createdAt = $createdAt;
    }

    public static function fromFacebook(string $userAppId, Calendar $calendar) : self
    {
        Assertion::betweenLength($userAppId, 0, 255);

        $user = new self($calendar->currentTime());
        $user->fbUserAppId = $userAppId;

        return $user;
    }

    public function id() : UuidInterface
    {
        return Uuid::fromString($this->id);
    }
}

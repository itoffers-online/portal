<?php

declare(strict_types=1);

namespace HireInSocial\Application\User;

use HireInSocial\Application\Assertion;
use HireInSocial\Application\System\Calendar;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class User
{
    private $id;
    private $fbUserAppId;
    private $createdAt;

    private function __construct(\DateTimeImmutable $createdAt)
    {
        $this->id = (string) Uuid::uuid4();
        $this->createdAt = $createdAt;
    }

    public static function fromFacebook(string $userAppId, Calendar $calendar) : self
    {
        Assertion::betweenLength($userAppId, 0, 255);

        $user = new self($calendar->currentTime());
        $user->fbUserAppId = $userAppId;

        return $user;
    }

    public function id(): UuidInterface
    {
        return Uuid::fromString($this->id);
    }
}

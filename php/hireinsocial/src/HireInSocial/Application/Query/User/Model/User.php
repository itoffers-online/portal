<?php

declare(strict_types=1);

namespace HireInSocial\Application\Query\User\Model;

final class User
{
    private $id;
    private $fbAppId;

    public function __construct(string $id, ?string $fbAppId)
    {
        $this->id = $id;
        $this->fbAppId = $fbAppId;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function fbAppId(): ?string
    {
        return $this->fbAppId;
    }
}

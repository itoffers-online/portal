<?php

declare(strict_types=1);

namespace HireInSocial\Application\User;

use Ramsey\Uuid\UuidInterface;

interface Users
{
    public function add(User $user) : void;
    public function getById(UuidInterface $id) : User;
    public function getByFB(string $userAppId) : User;
}

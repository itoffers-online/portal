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

use Ramsey\Uuid\UuidInterface;

interface Users
{
    public function add(User $user) : void;
    public function getById(UuidInterface $id) : User;
    public function getByFB(string $userAppId) : User;
}

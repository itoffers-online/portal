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

namespace ITOffers\Notifications\Application;

use ITOffers\Notifications\Application\User\User;
use Ramsey\Uuid\UuidInterface;

interface Users
{
    public function getById(UuidInterface $id) : User;
}

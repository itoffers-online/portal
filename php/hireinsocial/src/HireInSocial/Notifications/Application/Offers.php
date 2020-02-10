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

namespace HireInSocial\Notifications\Application;

use HireInSocial\Notifications\Application\Offer\Offer;
use Ramsey\Uuid\UuidInterface;

interface Offers
{
    public function getById(UuidInterface $id) : Offer;
}

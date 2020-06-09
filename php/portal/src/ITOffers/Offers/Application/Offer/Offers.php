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

namespace ITOffers\Offers\Application\Offer;

use ITOffers\Offers\Application\User\User;
use Ramsey\Uuid\UuidInterface;

interface Offers
{
    public function add(Offer $offer) : void;

    public function getById(UuidInterface $offerId) : Offer;

    public function postedBy(User $user, \Aeon\Calendar\Gregorian\DateTime $since) : UserOffers;
}

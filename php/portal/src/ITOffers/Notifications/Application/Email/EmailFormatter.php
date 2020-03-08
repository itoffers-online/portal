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

namespace ITOffers\Notifications\Application\Email;

use ITOffers\Notifications\Application\Offer\Offer;

interface EmailFormatter
{
    public function offerPostedSubject(Offer $offer) : string;

    public function offerPostedBody(Offer $offer) : string;

    public function extraOffersAddedSubject() : string;

    public function extraOffersAddedBody(int $expiresInDays, int $amount) : string;
}

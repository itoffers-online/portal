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

namespace ITOffers\Offers\Application\Twitter;

use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\Offer\Offer;

class Tweet
{
    private string $id;

    private string $jobOfferId;

    public function __construct(string $id, Offer $offer)
    {
        Assertion::notEmpty($id);

        $this->id = $id;
        $this->jobOfferId = $offer->id()->toString();
    }
}

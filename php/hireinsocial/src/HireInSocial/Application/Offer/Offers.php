<?php

declare(strict_types=1);

namespace HireInSocial\Application\Offer;

interface Offers
{
    public function add(Offer $offer) : void;
}

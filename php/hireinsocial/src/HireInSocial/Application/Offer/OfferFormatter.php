<?php

declare(strict_types=1);

namespace HireInSocial\Application\Offer;

interface OfferFormatter
{
    public function format(Offer $offer) : string;
}

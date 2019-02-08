<?php

declare(strict_types=1);

namespace HireInSocial\Application\Query\Offer;

use HireInSocial\Application\Query\Offer\Model\Offers;
use HireInSocial\Application\System\Query;

interface OfferQuery extends Query
{
    public function total() : int;

    public function find(OfferFilter $filter) : Offers;
}

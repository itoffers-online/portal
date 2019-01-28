<?php

declare(strict_types=1);

namespace HireInSocial\Application\Query\Offer;

use HireInSocial\Application\System\Query;

interface OfferQuery extends Query
{
    public function count() : int;
}

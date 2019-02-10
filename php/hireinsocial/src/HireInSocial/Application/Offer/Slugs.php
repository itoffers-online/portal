<?php

declare(strict_types=1);

namespace HireInSocial\Application\Offer;

interface Slugs
{
    public function add(Slug $slug) : void;
}

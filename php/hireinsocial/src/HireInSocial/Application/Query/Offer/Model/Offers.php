<?php

declare(strict_types=1);

namespace HireInSocial\Application\Query\Offer\Model;

final class Offers extends \ArrayObject
{
    public function __construct(Offer ...$offers)
    {
        parent::__construct($offers);
    }
}

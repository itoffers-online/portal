<?php

declare(strict_types=1);

namespace HireInSocial\Application\Query\Offer\Model;

final class Offers extends \ArrayObject
{
    public function __construct(Offer ...$specializations)
    {
        parent::__construct($specializations);
    }

    public function first() : Offer
    {
        return \current((array) $this);
    }
}

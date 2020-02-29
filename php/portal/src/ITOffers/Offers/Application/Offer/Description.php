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

use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\Offer\Description\Requirements;

final class Description
{
    private string $benefits;

    private Requirements $requirements;

    public function __construct(string $benefits, Requirements $requirements)
    {
        Assertion::betweenLength($benefits, 100, 2_048);

        $this->benefits = $benefits;
        $this->requirements = $requirements;
    }

    public function benefits() : string
    {
        return $this->benefits;
    }

    public function requirements() : Requirements
    {
        return $this->requirements;
    }
}

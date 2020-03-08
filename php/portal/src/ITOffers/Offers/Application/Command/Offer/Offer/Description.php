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

namespace ITOffers\Offers\Application\Command\Offer\Offer;

use ITOffers\Offers\Application\Command\Offer\Offer\Description\Requirements;

final class Description
{
    private string $technologyStack;

    private string $benefits;

    private Requirements $requirements;

    public function __construct(string $technologyStack, string $benefits, Requirements $requirements)
    {
        $this->technologyStack = $technologyStack;
        $this->requirements = $requirements;
        $this->benefits = $benefits;
    }

    public function requirements() : Requirements
    {
        return $this->requirements;
    }

    public function benefits() : string
    {
        return $this->benefits;
    }

    public function technologyStack() : string
    {
        return $this->technologyStack;
    }
}

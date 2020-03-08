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
use ITOffers\Offers\Application\Offer\Position\SeniorityLevels;

final class Position
{
    private int $seniorityLevel;

    private string $name;

    public function __construct(int $seniorityLevel, string $name)
    {
        Assertion::inArray($seniorityLevel, SeniorityLevels::all());
        Assertion::betweenLength($name, 3, 255);

        $this->seniorityLevel = $seniorityLevel;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function seniorityLevel() : int
    {
        return $this->seniorityLevel;
    }

    public function name() : string
    {
        return $this->name;
    }
}

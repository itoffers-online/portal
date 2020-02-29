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

final class Position
{
    private int $seniorityLevel;

    private string $name;

    private string $description;

    public function __construct(int $seniorityLevel, string $name, string $description)
    {
        $this->seniorityLevel = $seniorityLevel;
        $this->name = $name;
        $this->description = $description;
    }

    public function seniorityLevel() : int
    {
        return $this->seniorityLevel;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function description() : string
    {
        return $this->description;
    }
}

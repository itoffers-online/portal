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
    /**
     * @var int
     */
    private $seniorityLevel;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    public function __construct(int $seniorityLevel, string $name, string $description)
    {
        Assertion::inArray($seniorityLevel, SeniorityLevels::all());
        Assertion::betweenLength($name, 3, 255);
        Assertion::betweenLength($description, 50, 2048);

        $this->seniorityLevel = $seniorityLevel;
        $this->name = $name;
        $this->description = $description;
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

    public function description() : string
    {
        return $this->description;
    }
}

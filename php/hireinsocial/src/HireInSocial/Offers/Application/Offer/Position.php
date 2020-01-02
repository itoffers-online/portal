<?php

declare(strict_types=1);

/*
 * This file is part of the Hire in Social project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HireInSocial\Offers\Application\Offer;

use HireInSocial\Offers\Application\Assertion;
use HireInSocial\Offers\Application\Offer\Position\SeniorityLevels;

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
        Assertion::betweenLength($description, 50, 1024);

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

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

namespace HireInSocial\Offers\Application\Query\Offer\Model\Offer;

use HireInSocial\Offers\Application\Exception\Exception;

final class Description
{
    /**
     * @var int
     */
    private $seniorityLevel;

    /**
     * @var string
     */
    private $requirements;

    /**
     * @var string
     */
    private $benefits;

    public function __construct(int $seniorityLevel, string $requirements, string $benefits)
    {
        $this->seniorityLevel = $seniorityLevel;
        $this->requirements = $requirements;
        $this->benefits = $benefits;
    }

    public function seniorityLevel() : string
    {
        switch ($this->seniorityLevel) {
            case 0:
                return 'Intern';
            case 1:
                return 'Junior';
            case 2:
                return 'Mid';
            case 3:
                return 'Senior';
            case 4:
                return 'Expert';
            default:
                throw new Exception("Unknown seniority level");
        }
    }

    public function requirements() : string
    {
        return $this->requirements;
    }

    public function benefits() : string
    {
        return $this->benefits;
    }
}

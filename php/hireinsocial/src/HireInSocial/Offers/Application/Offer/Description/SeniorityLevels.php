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

namespace HireInSocial\Offers\Application\Offer\Description;

use HireInSocial\Offers\Application\Exception\Exception;

class SeniorityLevels
{
    public const INTERN = 0;

    public const JUNIOR = 1;

    public const MID = 2;

    public const SENIOR = 3;

    public const EXPERT = 4;

    /**
     * @return array<int>
     */
    public static function all() : array
    {
        return [
            self::INTERN,
            self::JUNIOR,
            self::MID,
            self::SENIOR,
            self::EXPERT,
        ];
    }

    public static function toString(int $level) : string
    {
        switch ($level) {
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
                throw new Exception(\sprintf("Unknown seniority level %d", $level));
        }
    }
}

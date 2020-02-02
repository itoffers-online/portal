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

namespace HireInSocial\Offers\Application\Command\Offer\Offer\Description;

use HireInSocial\Offers\Application\Command\Offer\Offer\Description\Requirements\Skill;

final class Requirements
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var Skill[]
     */
    private $skills;

    public function __construct(string $description, Skill ...$skills)
    {
        $this->description = $description;
        $this->skills = $skills;
    }

    /**
     * @return string
     */
    public function description() : string
    {
        return $this->description;
    }

    /**
     * @return Skill[]
     */
    public function skills() : array
    {
        return $this->skills;
    }
}

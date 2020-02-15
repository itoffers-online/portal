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

namespace ITOffers\Offers\Application\Query\Offer\Model\Offer\Description;

use ITOffers\Offers\Application\Query\Offer\Model\Offer\Description\Requirements\Skill;

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
        \uasort(
            $skills,
            function (Skill $skill, Skill $nextSkill) {
                return $nextSkill->experienceYears() <=> $skill->experienceYears();
            }
        );
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

    public function skills() : array
    {
        return $this->skills;
    }

    public function requiredSkills() : array
    {
        return \array_filter(
            $this->skills,
            function (Skill $skill) {
                return $skill->required();
            }
        );
    }

    public function niceToHaveSkills() : array
    {
        return \array_filter(
            $this->skills,
            function (Skill $skill) {
                return !$skill->required();
            }
        );
    }
}

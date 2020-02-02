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

use HireInSocial\Offers\Application\Assertion;
use HireInSocial\Offers\Application\Offer\Description\Requirements\Skill;

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
        Assertion::betweenLength($description, 100, 2048);

        Assertion::count(
            \array_unique(\array_map(function (Skill $skill) {
                return \mb_strtolower($skill->name());
            }, $skills)),
            \count($skills),
            \sprintf('Skills can\'t be duplicated: %s', \implode(', ', \array_map(function (Skill $skill) {
                return \mb_strtolower($skill->name());
            }, $skills)))
        );
        Assertion::maxCount($skills, 50, 'Can\'t add more than 50 skills');

        $this->description = $description;
        $this->skills = $skills;
    }
}

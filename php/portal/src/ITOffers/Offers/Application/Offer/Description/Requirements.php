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

namespace ITOffers\Offers\Application\Offer\Description;

use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\Offer\Description\Requirements\Skill;

final class Requirements
{
    private string $description;

    /**
     * @var Skill[]
     */
    private array $skills;

    public function __construct(string $description, Skill ...$skills)
    {
        Assertion::betweenLength(\strip_tags($description), 100, 2_048);

        Assertion::count(
            \array_unique(\array_map(fn (Skill $skill) => \mb_strtolower($skill->name()), $skills)),
            \count($skills),
            \sprintf('Skills can\'t be duplicated: %s', \implode(', ', \array_map(fn (Skill $skill) => \mb_strtolower($skill->name()), $skills)))
        );
        Assertion::maxCount($skills, 50, 'Can\'t add more than 50 skills');

        $this->description = $description;
        $this->skills = $skills;
    }
}

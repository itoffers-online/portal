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

namespace HireInSocial\Offers\Application\Offer\Description\Requirements;

final class Skill
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $required;

    /**
     * @var int|null
     */
    private $experienceYears;

    public function __construct(string $name, bool $required, ?int $experienceYears = null)
    {
        $this->name = $name;
        $this->required = $required;
        $this->experienceYears = $experienceYears;
    }

    public function name() : string
    {
        return $this->name;
    }
}

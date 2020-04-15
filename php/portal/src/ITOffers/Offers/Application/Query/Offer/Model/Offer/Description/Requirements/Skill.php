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

namespace ITOffers\Offers\Application\Query\Offer\Model\Offer\Description\Requirements;

final class Skill
{
    private string $name;

    private bool $required;

    private ?int $experienceYears = null;

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

    public function required() : bool
    {
        return $this->required;
    }

    public function experienceYears() : ?int
    {
        return $this->experienceYears;
    }
}

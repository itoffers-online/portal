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

namespace HireInSocial\Application\Command\Offer\Offer;

final class Description
{
    private $requirements;

    private $benefits;

    public function __construct(string $requirements, string $benefits)
    {
        $this->requirements = $requirements;
        $this->benefits = $benefits;
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

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

namespace HireInSocial\Offers\Application\Command\Offer\Offer;

use HireInSocial\Offers\Application\Command\Offer\Offer\Description\Requirements;

final class Description
{
    /**
     * @var Requirements
     */
    private $requirements;

    /**
     * @var string
     */
    private $benefits;

    public function __construct(string $benefits, Requirements $requirements)
    {
        $this->requirements = $requirements;
        $this->benefits = $benefits;
    }

    public function requirements() : Requirements
    {
        return $this->requirements;
    }

    public function benefits() : string
    {
        return $this->benefits;
    }
}

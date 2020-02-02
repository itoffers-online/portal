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

namespace HireInSocial\Offers\Application\Offer;

use HireInSocial\Offers\Application\Assertion;
use HireInSocial\Offers\Application\Offer\Description\Requirements;

final class Description
{
    /**
     * @var string
     */
    private $benefits;

    /**
     * @var Requirements
     */
    private $requirements;

    public function __construct(string $benefits, Requirements $requirements)
    {
        Assertion::betweenLength($benefits, 100, 2048);

        $this->benefits = $benefits;
        $this->requirements = $requirements;
    }

    public function benefits() : string
    {
        return $this->benefits;
    }

    public function requirements() : Requirements
    {
        return $this->requirements;
    }
}

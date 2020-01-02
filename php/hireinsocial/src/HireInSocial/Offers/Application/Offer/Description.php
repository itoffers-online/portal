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

final class Description
{
    /**
     * @var string
     */
    private $requirements;

    /**
     * @var string
     */
    private $benefits;

    public function __construct(string $requirements, string $benefits)
    {
        Assertion::betweenLength($requirements, 100, 1024);
        Assertion::betweenLength($benefits, 100, 1024);

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

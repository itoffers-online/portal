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

final class Channels
{
    /**
     * @var bool
     */
    private $facebookGroup;

    public function __construct(bool $facebookGroup)
    {
        $this->facebookGroup = $facebookGroup;
    }

    public function facebookGroup() : bool
    {
        return $this->facebookGroup;
    }
}

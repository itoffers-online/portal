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

use HireInSocial\Application\Command\Offer\Offer\Location\LatLng;

final class Location
{
    private $remote;

    private $name;

    private $latLng;

    public function __construct(bool $remote, ?string $name = null, ?LatLng $latLng = null)
    {
        $this->remote = $remote;
        $this->name = $name;
        $this->latLng = $latLng;
    }

    public function remote() : bool
    {
        return $this->remote;
    }

    public function name() : ?string
    {
        return $this->name;
    }

    public function latLng() : ?LatLng
    {
        return $this->latLng;
    }
}

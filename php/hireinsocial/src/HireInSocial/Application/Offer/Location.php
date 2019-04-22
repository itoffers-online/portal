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

namespace HireInSocial\Application\Offer;

use HireInSocial\Application\Assertion;

final class Location
{
    private $remote;
    private $name;
    private $lat;
    private $lng;

    private function __construct()
    {
    }

    public static function onlyRemote() : self
    {
        $location = new self();
        $location->remote = true;

        return $location;
    }

    public static function atPlace(bool $remote, string $name, float $lat, float $lng) : self
    {
        Assertion::betweenLength($name, 3, 512);
        Assertion::greaterOrEqualThan($lat, -90.0);
        Assertion::lessOrEqualThan($lat, 90.0);
        Assertion::greaterOrEqualThan($lng, -180.0);
        Assertion::lessOrEqualThan($lng, 180.0);

        $location = new self();
        $location->name = $name;
        $location->remote = $remote;
        $location->lat = $lat;
        $location->lng = $lng;

        return $location;
    }

    public function isRemote(): bool
    {
        return $this->remote;
    }

    public function name() : ?string
    {
        return $this->name;
    }
}

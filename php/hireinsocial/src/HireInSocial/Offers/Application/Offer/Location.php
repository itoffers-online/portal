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

final class Location
{
    /**
     * @var bool
     */
    private $remote;

    /**
     * @var string |null
     */
    private $countryCode;

    /**
     * @var string|null
     */
    private $city;

    /**
     * @var float
     */
    private $lat;

    /**
     * @var float
     */
    private $lng;

    private function __construct()
    {
    }

    public static function remote() : self
    {
        $location = new self();
        $location->remote = true;

        return $location;
    }

    public static function partiallyRemote(string $countryCode, string $city, float $lat, float $lng) : self
    {
        Assertion::length($countryCode, 2);
        Assertion::betweenLength($city, 3, 512);
        Assertion::greaterOrEqualThan($lat, -90.0);
        Assertion::lessOrEqualThan($lat, 90.0);
        Assertion::greaterOrEqualThan($lng, -180.0);
        Assertion::lessOrEqualThan($lng, 180.0);

        $location = new self();
        $location->countryCode = $countryCode;
        $location->city = $city;
        $location->remote = true;
        $location->lat = $lat;
        $location->lng = $lng;

        return $location;
    }

    public static function atOffice(string $countryCode, string $city, float $lat, float $lng) : self
    {
        Assertion::length($countryCode, 2);
        Assertion::betweenLength($city, 3, 512);
        Assertion::greaterOrEqualThan($lat, -90.0);
        Assertion::lessOrEqualThan($lat, 90.0);
        Assertion::greaterOrEqualThan($lng, -180.0);
        Assertion::lessOrEqualThan($lng, 180.0);

        $location = new self();
        $location->countryCode = $countryCode;
        $location->city = $city;
        $location->remote = false;
        $location->lat = $lat;
        $location->lng = $lng;

        return $location;
    }

    /**
     * @return string|null
     */
    public function countryCode() : ?string
    {
        return $this->countryCode;
    }

    public function city() : ?string
    {
        return $this->city;
    }

    public function isRemote() : bool
    {
        return $this->remote === true && $this->countryCode === null;
    }

    public function isAtOffice() : bool
    {
        return $this->remote === false && $this->countryCode !== null;
    }

    public function isPartiallyRemote() : bool
    {
        return $this->remote === true && $this->countryCode !== null;
    }

    public function lat() : float
    {
        return $this->lat;
    }

    public function lng() : float
    {
        return $this->lng;
    }
}

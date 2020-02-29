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

namespace ITOffers\Offers\Application\Offer;

use ITOffers\Offers\Application\Assertion;

final class Location
{
    /**
     * @var bool
     */
    private $remote;

    /**
     * @var string|null
     */
    private $countryCode;

    /**
     * @var string|null
     */
    private $city;

    /**
     * @var string|null
     */
    private $address;

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

    public static function partiallyRemote(string $countryCode, string $city, string $address, float $lat, float $lng) : self
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
        $location->address = $address;
        $location->remote = true;
        $location->lat = $lat;
        $location->lng = $lng;

        return $location;
    }

    public static function atOffice(string $countryCode, string $city, string $address, float $lat, float $lng) : self
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
        $location->address = $address;
        $location->remote = false;
        $location->lat = $lat;
        $location->lng = $lng;

        return $location;
    }

    public function countryCode() : ?string
    {
        return $this->countryCode;
    }

    public function city() : ?string
    {
        return $this->city;
    }

    public function address() : ?string
    {
        return $this->address;
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

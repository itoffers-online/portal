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

namespace ITOffers\Offers\Application\Command\Offer\Offer;

use ITOffers\Offers\Application\Command\Offer\Offer\Location\LatLng;

final class Location
{
    private bool $remote;

    private ?string $countryCode;

    private ?string $city;

    private ?string $address;

    private ?LatLng $latLng;

    public function __construct(bool $remote, ?string $countryCode = null, ?string $city = null, ?string $address = null, ?LatLng $latLng = null)
    {
        $this->remote = $remote;
        $this->countryCode = $countryCode;
        $this->city = $city;
        $this->address = $address;
        $this->latLng = $latLng;
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

    public function remote() : bool
    {
        return $this->remote;
    }

    public function latLng() : ?LatLng
    {
        return $this->latLng;
    }
}

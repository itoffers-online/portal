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
     * @var LatLng|null
     */
    private $latLng;

    public function __construct(bool $remote, ?string $countryCode = null, ?string $city = null, ?LatLng $latLng = null)
    {
        $this->remote = $remote;
        $this->countryCode = $countryCode;
        $this->city = $city;
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

    public function remote() : bool
    {
        return $this->remote;
    }

    public function latLng() : ?LatLng
    {
        return $this->latLng;
    }
}

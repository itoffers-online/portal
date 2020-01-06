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

namespace HireInSocial\Offers\Application\Query\Offer\Model\Offer;

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
     * @var float|null
     */
    private $lat;

    /**
     * @var float|null
     */
    private $lng;

    public function __construct(bool $remote, ?string $countryCode = null, ?string $city = null, ?float $lat = null, ?float $lng = null)
    {
        $this->remote = $remote;
        $this->lat = $lat;
        $this->lng = $lng;
        $this->countryCode = $countryCode;
        $this->city = $city;
    }

    public function remote() : bool
    {
        return $this->remote;
    }

    public function countryCode() : ?string
    {
        return $this->countryCode;
    }

    public function city() : ?string
    {
        return $this->city;
    }

    public function lat() : ?float
    {
        return $this->lat;
    }

    public function lng() : ?float
    {
        return $this->lng;
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
}

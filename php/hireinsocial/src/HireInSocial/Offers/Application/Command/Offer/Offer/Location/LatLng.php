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

namespace HireInSocial\Offers\Application\Command\Offer\Offer\Location;

final class LatLng
{
    /**
     * @var float
     */
    private $lat;

    /**
     * @var float
     */
    private $lng;

    public function __construct(float $lat, float $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
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

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

namespace HireInSocial\Application\Query\Offer\Model\Offer;

final class Location
{
    /**
     * @var bool
     */
    private $remote;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var float|null
     */
    private $lat;

    /**
     * @var float|null
     */
    private $lng;

    public function __construct(bool $remote, ?string $name = null, ?float $lat = null, ?float $lng = null)
    {
        $this->remote = $remote;
        $this->name = $name;
        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function remote() : bool
    {
        return $this->remote;
    }

    public function name() : ?string
    {
        return $this->name;
    }

    public function lat() : ?float
    {
        return $this->lat;
    }

    public function lng() : ?float
    {
        return $this->lng;
    }
}

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

    public function __construct(bool $remote, string $name = null)
    {
        if ($name) {
            Assertion::betweenLength($name, 3, 512);
        }

        $this->remote = $remote;
        $this->name = $name;
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

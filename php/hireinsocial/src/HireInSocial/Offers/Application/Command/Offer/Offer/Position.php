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

namespace HireInSocial\Offers\Application\Command\Offer\Offer;

final class Position
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    public function __construct(string $name, string $description)
    {
        $this->name = $name;
        $this->description = $description;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function description() : string
    {
        return $this->description;
    }
}
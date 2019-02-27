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

final class Contract
{
    private $type;

    public function __construct(string $type)
    {
        Assertion::notEmpty($type);
        Assertion::betweenLength($type, 3, 255);

        $this->type = $type;
    }

    public function type(): string
    {
        return $this->type;
    }
}

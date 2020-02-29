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

final class Contract
{
    private string $type;

    public function __construct(string $type)
    {
        Assertion::notEmpty($type);
        Assertion::betweenLength($type, 3, 255);

        $this->type = $type;
    }

    public function type() : string
    {
        return $this->type;
    }
}

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

namespace ITOffers\Offers\Infrastructure\PHP\Hash;

use ITOffers\Offers\Application\Hash\Encoder;

final class SHA256Encoder implements Encoder
{
    public function encode(string $value) : string
    {
        return \hash('sha256', $value);
    }
}

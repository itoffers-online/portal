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
use Symfony\Component\Intl\Locales;

final class Locale
{
    private string $code;

    public function __construct(string $code)
    {
        Assertion::true(Locales::exists($code));

        $this->code = $code;
    }

    public function __toString()
    {
        return (string) $this->code;
    }
}

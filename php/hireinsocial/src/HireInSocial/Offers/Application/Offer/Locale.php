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

namespace HireInSocial\Offers\Application\Offer;

use HireInSocial\Offers\Application\Assertion;
use Symfony\Component\Intl\Locales;

final class Locale
{
    /**
     * @var string
     */
    private $code;

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

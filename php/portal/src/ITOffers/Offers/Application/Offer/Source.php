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

final class Source
{
    /**
     * @var string
     */
    private string $url;

    public function __construct(string $url)
    {
        Assertion::betweenLength($url, 3, 2_083);

        $this->url = $url;
    }
}

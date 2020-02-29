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

namespace ITOffers\Offers\Application\Query\Offer\Model\Offer;

final class OfferPDF
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function __toString()
    {
        return $this->path;
    }

    public function url(string $baseUrl) : string
    {
        return rtrim($baseUrl, '/') . '/' . ltrim($this->path, '/');
    }
}

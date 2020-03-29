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

namespace ITOffers\Offers\Application\Command\Offer\Offer;

final class Company
{
    private string $name;

    private string $url;

    private string $description;

    private ?string $logoPath;

    public function __construct(string $name, string $url, string $description, ?string $logoPath = null)
    {
        $this->name = $name;
        $this->url = $url;
        $this->description = $description;
        $this->logoPath = $logoPath;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function url() : string
    {
        return $this->url;
    }

    public function description() : string
    {
        return $this->description;
    }

    public function logoPath() : ?string
    {
        return $this->logoPath;
    }
}

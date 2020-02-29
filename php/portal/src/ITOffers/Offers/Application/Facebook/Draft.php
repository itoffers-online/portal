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

namespace ITOffers\Offers\Application\Facebook;

use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\Offer\Offer;

final class Draft
{
    private string $message;

    private string $link;

    private function __construct(string $message, string $link)
    {
        Assertion::notEmpty($message);
        Assertion::url($link);

        $this->message = $message;
        $this->link = $link;
    }

    public static function createFor(Offer $offer, string $message) : self
    {
        return new self($message, $offer->company()->url());
    }

    public function __toString() : string
    {
        return $this->message;
    }

    public function link() : string
    {
        return $this->link;
    }
}

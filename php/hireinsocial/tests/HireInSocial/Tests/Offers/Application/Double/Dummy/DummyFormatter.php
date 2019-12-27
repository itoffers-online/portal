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

namespace HireInSocial\Tests\Offers\Application\Double\Dummy;

use HireInSocial\Offers\Application\Offer\Offer;
use HireInSocial\Offers\Application\Offer\OfferFormatter;

final class DummyFormatter implements OfferFormatter
{
    public function format(Offer $offer) : string
    {
        return 'This is dummy job offer';
    }
}

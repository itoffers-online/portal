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

namespace HireInSocial\Tests\Offers\Application\MotherObject\Facebook;

use HireInSocial\Offers\Application\Facebook\Draft;
use HireInSocial\Tests\Offers\Application\MotherObject\Offer\OfferMother;

final class DraftMother
{
    public static function random() : Draft
    {
        return Draft::createFor($offer = OfferMother::random(), 'This is some random facebook job offer description.');
    }
}

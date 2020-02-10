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

namespace HireInSocial\Tests\Notifications\Application\MotherObject;

use HireInSocial\Notifications\Application\Offer\Offer;
use Ramsey\Uuid\Uuid;

final class OfferMother
{
    public static function random() : Offer
    {
        return new Offer(
            Uuid::uuid4(),
            'recruiter@itoffers.online',
            'Recruiter',
            'some-offer-slug',
            'php',
            4,
            'Software Developer',
            'Company',
            'https://itoffers.online'
        );
    }
}

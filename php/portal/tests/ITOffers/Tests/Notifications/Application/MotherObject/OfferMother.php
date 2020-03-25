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

namespace ITOffers\Tests\Notifications\Application\MotherObject;

use ITOffers\Notifications\Application\Offer\Offer;
use ITOffers\Notifications\Application\Offer\Offer\Contact;
use Ramsey\Uuid\Uuid;

final class OfferMother
{
    public static function random() : Offer
    {
        return new Offer(
            Uuid::uuid4(),
            'recruiter@itoffers.online',
            'php',
            4,
            'Software Developer',
            'Company',
            'https://itoffers.online',
            Contact::externalSource('https://itoffers.online')
        );
    }
}

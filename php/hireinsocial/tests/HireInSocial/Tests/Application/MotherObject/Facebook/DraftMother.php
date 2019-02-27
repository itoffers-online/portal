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

namespace HireInSocial\Tests\Application\MotherObject\Facebook;

use HireInSocial\Application\Facebook\Draft;
use HireInSocial\Tests\Application\Double\Dummy\DummyFormatter;
use HireInSocial\Tests\Application\MotherObject\Offer\OfferMother;
use HireInSocial\Tests\Application\MotherObject\User\UserMother;
use Ramsey\Uuid\Uuid;

final class DraftMother
{
    public static function random() : Draft
    {
        return Draft::createFor(UserMother::withId(Uuid::uuid4()), new DummyFormatter(), OfferMother::random());
        ;
    }
}

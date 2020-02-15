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

namespace ITOffers\Tests\Offers\Application\MotherObject\Specialization;

use ITOffers\Offers\Application\Specialization\FacebookChannel;
use ITOffers\Offers\Application\Specialization\Specialization;
use ITOffers\Tests\Offers\Application\MotherObject\Facebook\GroupMother;
use ITOffers\Tests\Offers\Application\MotherObject\Facebook\PageMother;

final class SpecializationMother
{
    public static function create(string $spec) : Specialization
    {
        return new Specialization($spec);
    }

    public static function random() : Specialization
    {
        return new Specialization('php');
    }

    public static function withFacebook() : Specialization
    {
        $specialization = self::random();
        $specialization->setFacebook(new FacebookChannel(PageMother::random(), GroupMother::random()));

        return $specialization;
    }
}

<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\MotherObject\Specialization;

use HireInSocial\Application\Specialization\FacebookChannel;
use HireInSocial\Application\Specialization\Specialization;
use HireInSocial\Tests\Application\MotherObject\Facebook\GroupMother;
use HireInSocial\Tests\Application\MotherObject\Facebook\PageMother;

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

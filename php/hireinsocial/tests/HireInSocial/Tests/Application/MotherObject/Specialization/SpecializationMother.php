<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\MotherObject\Specialization;

use HireInSocial\Application\Specialization\FacebookChannel;
use HireInSocial\Application\Specialization\Specialization;
use HireInSocial\Tests\Application\MotherObject\Facebook\GroupMother;
use HireInSocial\Tests\Application\MotherObject\Facebook\PageMother;

final class SpecializationMother
{
    public static function create(string $spec)
    {
        return new Specialization($spec, new FacebookChannel(PageMother::random(), GroupMother::random()));
    }

    public static function random()
    {
        return new Specialization('php', new FacebookChannel(PageMother::random(), GroupMother::random()));
    }
}

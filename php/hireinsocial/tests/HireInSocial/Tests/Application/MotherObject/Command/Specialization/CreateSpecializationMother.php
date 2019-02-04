<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\MotherObject\Command\Specialization;

use HireInSocial\Application\Command\Specialization\CreateSpecialization;

final class CreateSpecializationMother
{
    public static function create(string $slug) : CreateSpecialization
    {
        return new CreateSpecialization(
            $slug,
            uniqid('facebook_page_id'),
            uniqid('facebook_page_token'),
            uniqid('facebook_group_id')
        );
    }

    public static function random() : CreateSpecialization
    {
        return self::create('slug');
    }
}

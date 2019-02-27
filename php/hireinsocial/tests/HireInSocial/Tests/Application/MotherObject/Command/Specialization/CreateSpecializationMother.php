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

namespace HireInSocial\Tests\Application\MotherObject\Command\Specialization;

use HireInSocial\Application\Command\Specialization\CreateSpecialization;

final class CreateSpecializationMother
{
    public static function create(string $slug) : CreateSpecialization
    {
        return new CreateSpecialization(
            $slug
        );
    }

    public static function random() : CreateSpecialization
    {
        return self::create('slug');
    }
}

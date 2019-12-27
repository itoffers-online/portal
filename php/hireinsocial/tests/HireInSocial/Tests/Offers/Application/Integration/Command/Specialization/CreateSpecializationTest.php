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

namespace HireInSocial\Tests\Offers\Application\Integration\Command\Specialization;

use HireInSocial\Offers\Application\Command\Specialization\CreateSpecialization;
use HireInSocial\Tests\Offers\Application\Integration\HireInSocialTestCase;

final class CreateSpecializationTest extends HireInSocialTestCase
{
    public function test_create_specialization() : void
    {
        $slug = 'php';

        $this->systemContext->offersFacade()->handle(new CreateSpecialization($slug));

        $this->assertTrue(
            $this->systemContext->offersFacade()->specializationQuery()->all()->has($slug)
        );
        $this->assertCount(
            1,
            $this->systemContext->offersFacade()->specializationQuery()->all()
        );
    }
}

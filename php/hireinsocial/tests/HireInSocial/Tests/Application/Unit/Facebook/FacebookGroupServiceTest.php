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

namespace HireInSocial\Tests\Application\Unit\Facebook;

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\Facebook\Facebook;
use HireInSocial\Application\Facebook\FacebookGroupService;
use HireInSocial\Tests\Application\MotherObject\Facebook\DraftMother;
use HireInSocial\Tests\Application\MotherObject\Specialization\SpecializationMother;
use PHPUnit\Framework\TestCase;

final class FacebookGroupServiceTest extends TestCase
{
    public function test_post_as_a_page_for_specialization_without_facebook_channel() : void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Specialization "php" does not have facebook channel assigned.');

        $facebook = $this->createMock(Facebook::class);

        $service = new FacebookGroupService($facebook);

        $service->pagePostAtGroup(
            DraftMother::random(),
            SpecializationMother::create('php')
        );
    }
}

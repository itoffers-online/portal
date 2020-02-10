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

namespace ITOffers\Tests\Offers\Application\Unit\Facebook;

use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Offers\Application\Facebook\Facebook;
use ITOffers\Offers\Application\Facebook\FacebookGroupService;
use ITOffers\Tests\Offers\Application\MotherObject\Facebook\DraftMother;
use ITOffers\Tests\Offers\Application\MotherObject\Specialization\SpecializationMother;
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

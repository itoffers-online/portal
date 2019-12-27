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

namespace HireInSocial\Tests\Offers\Application\Unit\Specialization;

use HireInSocial\Offers\Application\Exception\InvalidAssertionException;
use HireInSocial\Offers\Application\Specialization\FacebookChannel;
use HireInSocial\Offers\Application\Specialization\Specialization;
use HireInSocial\Tests\Offers\Application\MotherObject\Facebook\GroupMother;
use HireInSocial\Tests\Offers\Application\MotherObject\Facebook\PageMother;
use PHPUnit\Framework\TestCase;

final class SpecializationTest extends TestCase
{
    /**
     * @dataProvider invalidSlugs
     */
    public function test_creating_specialization_with_invalid_slug(string $slug) : void
    {
        $this->expectException(InvalidAssertionException::class);

        new Specialization($slug);
    }

    public function test_that_slug_belongs_to_the_organization() : void
    {
        $this->assertTrue(
            (new Specialization('php-developers'))->is('PHP-Developers')
        );
    }

    public function test_setting_facebook_channel() : void
    {
        $specialization = new Specialization('php');
        $specialization->setFacebook(new FacebookChannel(PageMother::random(), GroupMother::random()));

        $this->assertInstanceOf(FacebookChannel::class, $specialization->facebookChannel());
    }

    public function invalidSlugs() : array
    {
        return [
            ['inalid.slug'],
            [''],
            ['invalid/slug'],
        ];
    }
}

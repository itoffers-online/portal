<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Unit\Specialization;

use HireInSocial\Application\Exception\InvalidAssertionException;
use HireInSocial\Application\Specialization\FacebookChannel;
use HireInSocial\Application\Specialization\Specialization;
use HireInSocial\Tests\Application\MotherObject\Facebook\GroupMother;
use HireInSocial\Tests\Application\MotherObject\Facebook\PageMother;
use PHPUnit\Framework\TestCase;

final class SpecializationTest extends TestCase
{
    /**
     * @dataProvider invalidSlugs
     */
    public function test_creating_specialization_with_invalid_slug(string $slug)
    {
        $this->expectException(InvalidAssertionException::class);

        new Specialization($slug, new FacebookChannel(PageMother::random(), GroupMother::random()));
    }

    public function test_that_slug_belongs_to_the_organization()
    {
        $this->assertTrue(
            (new Specialization('php-developers', new FacebookChannel(PageMother::random(), GroupMother::random())))->is('PHP-Developers')
        );
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

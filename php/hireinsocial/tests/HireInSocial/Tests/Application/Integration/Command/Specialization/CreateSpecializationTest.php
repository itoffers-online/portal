<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Integration\Command\Specialization;

use HireInSocial\Application\Command\Specialization\CreateSpecialization;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Tests\Application\Integration\HireInSocialTestCase;

final class CreateSpecializationTest extends HireInSocialTestCase
{
    public function test_create_specialization()
    {
        $slug = 'php';

        $this->systemContext->system()->handle(new CreateSpecialization($slug));

        $this->assertTrue(
            $this->systemContext->system()->query(SpecializationQuery::class)->all()->has($slug)
        );
        $this->assertCount(
            1,
            $this->systemContext->system()->query(SpecializationQuery::class)->all()
        );
    }
}

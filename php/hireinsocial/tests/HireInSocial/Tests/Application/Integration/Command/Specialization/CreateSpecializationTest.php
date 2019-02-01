<?php

declare (strict_types=1);

namespace HireInSocial\Tests\Application\Integration\Command\Specialization;

use HireInSocial\Application\Command\Specialization\CreateSpecialization;
use HireInSocial\Application\Query\Specialization\Model\Specialization;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Tests\Application\Integration\HireInSocialTestCase;

final class CreateSpecializationTest extends HireInSocialTestCase
{
    public function test_create_specialization()
    {
        $slug = 'php';

        $this->systemContext->system()->handle(new CreateSpecialization(
            $slug, 'PHP Developers', '123455678', uniqid('facebook'), '1234567'
        ));

        $this->assertInstanceOf(
            Specialization::class,
            $this->systemContext->system()->query(SpecializationQuery::class)->findBySlug($slug)
        );
        $this->assertCount(
            1,
            $this->systemContext->system()->query(SpecializationQuery::class)->all()
        );
    }
}
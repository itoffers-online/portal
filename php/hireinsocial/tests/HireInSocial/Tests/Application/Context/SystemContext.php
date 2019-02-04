<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Context;

use HireInSocial\Application\Query\Specialization\Model\Specialization;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\System;
use HireInSocial\Tests\Application\MotherObject\Command\Facebook\Page\PostToGroupMother;
use HireInSocial\Tests\Application\MotherObject\Command\Specialization\CreateSpecializationMother;

final class SystemContext
{
    private $system;

    public function __construct(System $system)
    {
        $this->system = $system;
    }

    public function system() : System
    {
        return $this->system;
    }

    public function createSpecialization(string $slug) : Specialization
    {
        $this->system->handle(CreateSpecializationMother::create($slug));

        return $this->system->query(SpecializationQuery::class)->findBySlug($slug);
    }

    public function postToFacebookGroup(string $fbUserId) : void
    {
        $createSpecialization = CreateSpecializationMother::random();
        $this->system->handle($createSpecialization);

        $this->system->handle(PostToGroupMother::postAs($fbUserId, $createSpecialization->slug()));
    }
}

<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Context;

use HireInSocial\Application\Command\Throttle\RemoveThrottle;
use HireInSocial\Application\Query\Specialization\Model\Specialization;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\System;
use HireInSocial\Tests\Application\MotherObject\Command\Offer\PostOfferMother;
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

    public function postToFacebookGroup(string $fbUserId, string $specialization) : void
    {
        $this->system->handle(PostOfferMother::postAs($fbUserId, $specialization));
    }

    public function removeThrottle(string $fbUserId) : void
    {
        $this->system->handle(new RemoveThrottle($fbUserId));
    }
}

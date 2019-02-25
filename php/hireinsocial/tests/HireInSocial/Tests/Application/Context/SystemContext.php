<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Context;

use HireInSocial\Application\Command\Throttle\RemoveThrottle;
use HireInSocial\Application\Command\User\FacebookConnect;
use HireInSocial\Application\Query\Specialization\Model\Specialization;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\Query\User\Model\User;
use HireInSocial\Application\Query\User\UserQuery;
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

    public function createUser() : User
    {
        $fbUserAppId = \uniqid('facebook_user_id');
        $this->system->handle(new FacebookConnect($fbUserAppId));

        return $this->system->query(UserQuery::class)->findByFacebook($fbUserAppId);
    }

    public function createSpecialization(string $slug) : Specialization
    {
        $this->system->handle(CreateSpecializationMother::create($slug));

        return $this->system->query(SpecializationQuery::class)->findBySlug($slug);
    }

    public function removeThrottle(string $fbUserId) : void
    {
        $this->system->handle(new RemoveThrottle($fbUserId));
    }
}

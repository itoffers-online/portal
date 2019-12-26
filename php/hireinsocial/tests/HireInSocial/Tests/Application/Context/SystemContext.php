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

namespace HireInSocial\Tests\Application\Context;

use HireInSocial\Application\Command\User\FacebookConnect;
use HireInSocial\Application\Query\Specialization\Model\Specialization;
use HireInSocial\Application\Query\User\Model\User;
use HireInSocial\Offers;
use HireInSocial\Tests\Application\MotherObject\Command\Specialization\CreateSpecializationMother;

final class SystemContext
{
    private $offers;

    public function __construct(Offers $offers)
    {
        $this->offers = $offers;
    }

    public function offersFacade() : Offers
    {
        return $this->offers;
    }

    public function createUser() : User
    {
        $fbUserAppId = \uniqid('facebook_user_id');
        $this->offers->handle(new FacebookConnect($fbUserAppId));

        return $this->offers->userQuery()->findByFacebook($fbUserAppId);
    }

    public function createSpecialization(string $slug) : Specialization
    {
        $this->offers->handle(CreateSpecializationMother::create($slug));

        return $this->offers->specializationQuery()->findBySlug($slug);
    }
}

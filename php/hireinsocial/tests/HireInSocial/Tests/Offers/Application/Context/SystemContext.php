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

namespace HireInSocial\Tests\Offers\Application\Context;

use HireInSocial\Offers\Application\Command\User\FacebookConnect;
use HireInSocial\Offers\Application\Query\Specialization\Model\Specialization;
use HireInSocial\Offers\Application\Query\User\Model\User;
use HireInSocial\Offers\Offers;
use HireInSocial\Tests\Offers\Application\MotherObject\Command\Specialization\CreateSpecializationMother;

final class SystemContext
{
    /**
     * @var Offers
     */
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

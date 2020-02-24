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

namespace ITOffers\Tests\Offers\Application\Context;

use Faker\Factory;
use ITOffers\Offers\Application\Command\User\AddExtraOffers;
use ITOffers\Offers\Application\Command\User\AddOfferAutoRenews;
use ITOffers\Offers\Application\Command\User\FacebookConnect;
use ITOffers\Offers\Application\Query\Offer\Model\Offer;
use ITOffers\Offers\Application\Query\Specialization\Model\Specialization;
use ITOffers\Offers\Application\Query\User\Model\User;
use ITOffers\Offers\Offers;
use ITOffers\Tests\Offers\Application\MotherObject\Command\Offer\PostOfferMother;
use ITOffers\Tests\Offers\Application\MotherObject\Command\Specialization\CreateSpecializationMother;
use Ramsey\Uuid\Uuid;

final class OffersContext
{
    /**
     * @var Offers
     */
    private $offers;

    public function __construct(Offers $offers)
    {
        $this->offers = $offers;
    }

    public function module() : Offers
    {
        return $this->offers;
    }

    public function createUser() : User
    {
        $fbUserAppId = \uniqid('facebook_user_id');
        $email = Factory::create()->email;
        $this->offers->handle(new FacebookConnect($fbUserAppId, $email));

        return $this->offers->userQuery()->findByFacebook($fbUserAppId);
    }

    public function createSpecialization(string $slug) : Specialization
    {
        $this->offers->handle(CreateSpecializationMother::create($slug));

        return $this->offers->specializationQuery()->findBySlug($slug);
    }

    public function addExtraOffer(User $user, int $expiresInDays) : void
    {
        $this->offers->handle(new AddExtraOffers($user->id(), 1, $expiresInDays));
    }

    public function addOfferAutRenewOffer(User $user, int $expiresInDays) : void
    {
        $this->offers->handle(new AddOfferAutoRenews($user->id(), 1, $expiresInDays));
    }

    public function createOffer(string $userId, string $specializationSlug) : Offer
    {
        $this->offers->handle(PostOfferMother::random($offerId = Uuid::uuid4()->toString(), $userId, $specializationSlug));

        return $this->offers->offerQuery()->findById($offerId);
    }
}

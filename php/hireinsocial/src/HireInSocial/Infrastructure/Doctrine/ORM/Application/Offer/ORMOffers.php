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

namespace HireInSocial\Infrastructure\Doctrine\ORM\Application\Offer;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use HireInSocial\Application\Offer\Offer;
use HireInSocial\Application\Offer\Offers;
use HireInSocial\Application\Offer\UserOffers;
use HireInSocial\Application\User\User;
use Ramsey\Uuid\UuidInterface;

final class ORMOffers implements Offers
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(Offer $offer) : void
    {
        $this->entityManager->persist($offer);
    }

    public function getById(UuidInterface $offerId) : Offer
    {
        return $this->entityManager->getRepository(Offer::class)->findOneBy(['id' => $offerId->toString()]);
    }

    public function postedBy(User $user, \DateTimeImmutable $since) : UserOffers
    {
        $criteria = new Criteria();
        $criteria
            ->where($criteria->expr()->eq('userId', $user->id()))
            ->andWhere($criteria->expr()->gt('createdAt', $since));

        return new UserOffers(
            $user,
            $since,
            ...$this->entityManager->getRepository(Offer::class)->matching($criteria)
        );
    }
}

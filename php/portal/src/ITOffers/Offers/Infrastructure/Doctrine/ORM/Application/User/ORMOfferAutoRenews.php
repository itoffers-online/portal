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

namespace ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\User;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Aeon\Calendar\Gregorian\Calendar;
use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Offers\Application\Offer\Offer;
use ITOffers\Offers\Application\User\OfferAutoRenew;
use ITOffers\Offers\Application\User\OfferAutoRenews;
use ITOffers\Offers\Application\User\User;
use Ramsey\Uuid\UuidInterface;

final class ORMOfferAutoRenews implements OfferAutoRenews
{
    private EntityManager $entityManager;

    private Calendar $calendar;

    public function __construct(EntityManager $entityManager, Calendar $calendar)
    {
        $this->entityManager = $entityManager;
        $this->calendar = $calendar;
    }

    public function add(OfferAutoRenew ...$offerAutoRenews) : void
    {
        foreach ($offerAutoRenews as $offerAutoRenew) {
            $this->entityManager->persist($offerAutoRenew);
        }
    }

    public function getUnassignedClosesToExpire(User $user) : OfferAutoRenew
    {
        $criteria = new Criteria();
        $criteria
            ->where($criteria->expr()->eq('userId', $user->id()->toString()))
            ->andWhere($criteria->expr()->gt('expiresAt', $this->calendar->now()))
            ->andWhere($criteria->expr()->isNull('renewedAt'))
            ->andWhere($criteria->expr()->isNull('offerId'))
            ->orderBy(['expiresAt' => 'ASC']);

        $offerAutoRenew = $this->entityManager->getRepository(OfferAutoRenew::class)->matching($criteria)->first();

        if (!$offerAutoRenew) {
            throw new Exception(\sprintf("User %s does not have any offer auto renews.", $user->email()));
        }

        return $offerAutoRenew;
    }

    public function getUnusedFor(UuidInterface $offerId) : OfferAutoRenew
    {
        $criteria = new Criteria();
        $criteria
            ->where($criteria->expr()->eq('offerId', $offerId->toString()))
            ->andWhere($criteria->expr()->isNull('renewedAt'))
            ->orderBy(['expiresAt' => 'ASC']);

        $offerAutoRenew = $this->entityManager->getRepository(OfferAutoRenew::class)->matching($criteria)->first();

        if (!$offerAutoRenew) {
            throw new Exception(\sprintf("Offer %s does not have any offer auto renews.", $offerId->toString()));
        }

        return $offerAutoRenew;
    }

    public function countUnassignedNotExpired(UuidInterface $userId) : int
    {
        $criteria = new Criteria();
        $criteria
            ->where($criteria->expr()->eq('userId', $userId->toString()))
            ->andWhere($criteria->expr()->gt('expiresAt', $this->calendar->now()))
            ->andWhere($criteria->expr()->isNull('renewedAt'))
            ->andWhere($criteria->expr()->isNull('offerId'));

        return $this->entityManager->getRepository(OfferAutoRenew::class)->matching($criteria)->count();
    }

    public function countAssignedTo(Offer $offer) : int
    {
        $criteria = new Criteria();
        $criteria
            ->andWhere($criteria->expr()->eq('offerId', $offer->id()->toString()));

        return $this->entityManager->getRepository(OfferAutoRenew::class)->matching($criteria)->count();
    }
}

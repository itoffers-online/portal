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
use ITOffers\Offers\Application\User\OfferAutoRenew;
use ITOffers\Offers\Application\User\OfferAutoRenews;
use Ramsey\Uuid\UuidInterface;

final class ORMOfferAutoRenews implements OfferAutoRenews
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(OfferAutoRenew ...$offerAutoRenews) : void
    {
        foreach ($offerAutoRenews as $offerAutoRenew) {
            $this->entityManager->persist($offerAutoRenew);
        }
    }

    public function findUnassignedClosesToExpire(UuidInterface $userId) : ?OfferAutoRenew
    {
        $criteria = new Criteria();
        $criteria
            ->where($criteria->expr()->eq('userId', $userId->toString()))
            ->andWhere($criteria->expr()->gt('expiresAt', new \DateTimeImmutable('now', new \DateTimeZone('UTC'))))
            ->andWhere($criteria->expr()->isNull('renewedAt'))
            ->andWhere($criteria->expr()->isNull('offerId'))
            ->orderBy(['expiresAt' => 'ASC']);

        $offerAutoRenew = $this->entityManager->getRepository(OfferAutoRenew::class)->matching($criteria)->first();

        return ($offerAutoRenew) ? $offerAutoRenew : null;
    }

    public function countNotAssignedNotExpired(UuidInterface $userId) : int
    {
        $criteria = new Criteria();
        $criteria
            ->where($criteria->expr()->eq('userId', $userId->toString()))
            ->andWhere($criteria->expr()->gt('expiresAt', new \DateTimeImmutable('now', new \DateTimeZone('UTC'))))
            ->andWhere($criteria->expr()->isNull('renewedAt'))
            ->andWhere($criteria->expr()->isNull('offerId'));

        return $this->entityManager->getRepository(OfferAutoRenew::class)->matching($criteria)->count();
    }
}

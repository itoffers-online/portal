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

namespace HireInSocial\Offers\Infrastructure\Doctrine\ORM\Application\User;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use HireInSocial\Offers\Application\User\ExtraOffer;
use HireInSocial\Offers\Application\User\ExtraOffers;
use Ramsey\Uuid\UuidInterface;

final class ORMExtraOffers implements ExtraOffers
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(ExtraOffer ...$extraOffers) : void
    {
        foreach ($extraOffers as $extraOffer) {
            $this->entityManager->persist($extraOffer);
        }
    }

    public function findClosesToExpire(UuidInterface $userId) : ?ExtraOffer
    {
        $criteria = new Criteria();
        $criteria
            ->where($criteria->expr()->eq('userId', $userId->toString()))
            ->andWhere($criteria->expr()->gt('expiresAt', new \DateTimeImmutable('now', new \DateTimeZone('UTC'))))
            ->andWhere($criteria->expr()->isNull('usedAt'))
            ->orderBy(['expiresAt' => 'ASC']);

        $extraOffer = $this->entityManager->getRepository(ExtraOffer::class)->matching($criteria)->first();

        return ($extraOffer) ? $extraOffer : null;
    }

    public function countNotExpired(UuidInterface $userId) : int
    {
        $criteria = new Criteria();
        $criteria
            ->where($criteria->expr()->eq('userId', $userId->toString()))
            ->andWhere($criteria->expr()->gt('expiresAt', new \DateTimeImmutable('now', new \DateTimeZone('UTC'))))
            ->andWhere($criteria->expr()->isNull('usedAt'));

        return $this->entityManager->getRepository(ExtraOffer::class)->matching($criteria)->count();
    }
}

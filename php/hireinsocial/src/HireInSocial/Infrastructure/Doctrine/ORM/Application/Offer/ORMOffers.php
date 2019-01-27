<?php

declare(strict_types=1);

namespace HireInSocial\Infrastructure\Doctrine\ORM\Application\Offer;

use Doctrine\ORM\EntityManager;
use HireInSocial\Application\Offer\Offer;
use HireInSocial\Application\Offer\Offers;

final class ORMOffers implements Offers
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(Offer $offer): void
    {
        $this->entityManager->persist($offer);
    }
}

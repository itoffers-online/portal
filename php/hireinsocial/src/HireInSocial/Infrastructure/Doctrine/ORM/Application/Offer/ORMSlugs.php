<?php

declare(strict_types=1);

namespace HireInSocial\Infrastructure\Doctrine\ORM\Application\Offer;

use Doctrine\ORM\EntityManager;
use HireInSocial\Application\Offer\Slug;
use HireInSocial\Application\Offer\Slugs;

final class ORMSlugs implements Slugs
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(Slug $slug): void
    {
        $this->entityManager->persist($slug);
    }
}

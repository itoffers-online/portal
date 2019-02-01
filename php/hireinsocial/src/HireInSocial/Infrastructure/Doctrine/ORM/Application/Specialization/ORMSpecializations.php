<?php

declare (strict_types=1);

namespace HireInSocial\Infrastructure\Doctrine\ORM\Application\Specialization;

use Doctrine\ORM\EntityManager;
use HireInSocial\Application\Specialization\Specialization;
use HireInSocial\Application\Specialization\Specializations;

final class ORMSpecializations implements Specializations
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function add(Specialization $specialization): void
    {
        $this->entityManager->persist($specialization);
    }

    public function get(string $slug): Specialization
    {
        return $this->entityManager->getRepository(Specialization::class)->findOneBy(['slug' => $slug]);
    }
}
<?php

declare (strict_types=1);

namespace HireInSocial\Infrastructure\Doctrine\ORM\Application\System;

use Doctrine\ORM\EntityManagerInterface;
use HireInSocial\Application\System\TransactionManager;

final class ORMTransactionManager implements TransactionManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function begin() : void
    {
        $this->entityManager->beginTransaction();
    }

    public function commit() : void
    {
        $this->entityManager->flush();
        $this->entityManager->commit();
        $this->entityManager->clear();
    }

    public function rollback() : void
    {
        $this->entityManager->rollback();
    }
}
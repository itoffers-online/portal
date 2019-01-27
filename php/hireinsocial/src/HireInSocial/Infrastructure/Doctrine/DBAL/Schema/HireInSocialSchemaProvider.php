<?php

declare(strict_types=1);

namespace HireInSocial\Infrastructure\Doctrine\DBAL\Schema;

use Doctrine\DBAL\Migrations\Provider\OrmSchemaProvider;
use Doctrine\DBAL\Migrations\Provider\SchemaProviderInterface;
use Doctrine\ORM\EntityManager;

final class HireInSocialSchemaProvider implements SchemaProviderInterface
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createSchema()
    {
        $schema = (new OrmSchemaProvider($this->entityManager))->createSchema();
        $schema->createNamespace('public');

        return $schema;
    }
}

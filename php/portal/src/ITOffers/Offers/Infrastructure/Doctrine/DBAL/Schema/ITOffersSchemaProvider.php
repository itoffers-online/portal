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

namespace ITOffers\Offers\Infrastructure\Doctrine\DBAL\Schema;

use Doctrine\DBAL\Migrations\Provider\OrmSchemaProvider;
use Doctrine\DBAL\Migrations\Provider\SchemaProviderInterface;
use Doctrine\ORM\EntityManager;

final class ITOffersSchemaProvider implements SchemaProviderInterface
{
    private EntityManager $entityManager;

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

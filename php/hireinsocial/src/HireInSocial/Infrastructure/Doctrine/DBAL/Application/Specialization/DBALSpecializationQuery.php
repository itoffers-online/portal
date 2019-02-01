<?php

declare (strict_types=1);

namespace HireInSocial\Infrastructure\Doctrine\DBAL\Application\Specialization;

use Doctrine\DBAL\Connection;
use HireInSocial\Application\Query\Specialization\Model\Specialization;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;

final class DBALSpecializationQuery implements SpecializationQuery
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function all(): array
    {
        return \array_map(
            [$this, 'hydrateSpecialization'],
            $this->connection->fetchAll('SELECT * FROM his_specialization ORDER BY slug')
        );
    }

    public function findBySlug(string $slug): ?Specialization
    {
        $specialization = $this->connection->fetchAssoc('SELECT * FROM his_specialization WHERE slug = :slug', ['slug' => $slug]);

        if (!$specialization) {
            return null;
        }

        return $this->hydrateSpecialization($specialization);
    }

    function hydrateSpecialization(array $data): Specialization
    {
        return new Specialization(
            $data['slug'],
            $data['name']
        );
    }
}
<?php

declare (strict_types=1);

namespace HireInSocial\Infrastructure\Doctrine\DBAL\Application\Query;

use Doctrine\DBAL\Connection;
use HireInSocial\Application\Query\Offer\OfferQuery;

final class DbalOfferQuery implements OfferQuery
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function count(): int
    {
        return (int) $this->connection->executeQuery('SELECT COUNT(*) FROM his_job_offer')->fetchColumn();
    }
}
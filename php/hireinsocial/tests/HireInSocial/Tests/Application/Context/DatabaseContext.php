<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Context;

use Doctrine\DBAL\Connection;

final class DatabaseContext
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function purgeDatabase() : void
    {
        foreach ($this->connection->getSchemaManager()->listTables() as $table) {
            if ($table->getName() === 'his_db_migration') {
                continue ;
            }

            $this->connection->query(sprintf('TRUNCATE TABLE %s', $table->getName()));
        }
    }
}

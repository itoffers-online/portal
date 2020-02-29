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

namespace ITOffers\Tests\Offers\Application\Context;

use Doctrine\DBAL\Connection;

final class DatabaseContext
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function purgeDatabase() : void
    {
        foreach ($this->connection->getSchemaManager()->listTables() as $table) {
            if ($table->getName() === 'itof_db_migration') {
                continue ;
            }

            $this->connection->query(sprintf('TRUNCATE TABLE %s', $table->getName()));
        }
    }
}

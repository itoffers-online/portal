<?php

declare (strict_types=1);

namespace HireInSocial\Tests\Application\Double\Dummy;

use HireInSocial\Application\System\TransactionManager;

final class DummyTransactionManager implements TransactionManager
{
    public function begin(): void
    {
    }

    public function commit(): void
    {
    }

    public function rollback(): void
    {
    }
}
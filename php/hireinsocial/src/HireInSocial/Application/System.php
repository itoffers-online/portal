<?php

declare(strict_types=1);

namespace HireInSocial\Application;

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\System\Command;
use HireInSocial\Application\System\CommandBus;
use HireInSocial\Application\System\Queries;
use HireInSocial\Application\System\Query;
use Psr\Log\LoggerInterface;

class System
{
    private $commandBus;
    private $queries;
    private $logger;

    public function __construct(CommandBus $commandBus, Queries $queries, LoggerInterface $logger)
    {
        $this->commandBus = $commandBus;
        $this->queries = $queries;
        $this->logger = $logger;
    }

    public function handle(Command $command) : void
    {
        try {
            $this->commandBus->handle($command);
        } catch (\Throwable $exception) {
            $this->logger->error(sprintf('Failed to handle command %s', \get_class($command)), [
                'exception' => \get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]);

            throw new Exception($exception->getMessage(), $exception->getCode());
        }
    }

    public function query(string $queryClass) : Query
    {
        return $this->queries->get($queryClass);
    }
}

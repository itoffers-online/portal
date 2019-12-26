<?php

declare(strict_types=1);

/*
 * This file is part of the Hire in Social project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HireInSocial\Application;

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\System\Calendar;
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

    private $calendar;

    public function __construct(CommandBus $commandBus, Queries $queries, LoggerInterface $logger, Calendar $calendar)
    {
        $this->commandBus = $commandBus;
        $this->queries = $queries;
        $this->logger = $logger;
        $this->calendar = $calendar;
    }

    public function handle(Command $command) : void
    {
        try {
            $this->commandBus->handle($command);
        } catch (\Throwable $exception) {
            $this->logger->error(sprintf('Failed to handle command %s', \get_class($command)), [
                'system_time' => $this->calendar->currentTime()->format('c'),
                'exception' => \get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'trace' => $exception->getTraceAsString(),
            ]);

            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function query(string $queryClass) : Query
    {
        return $this->queries->get($queryClass);
    }
}

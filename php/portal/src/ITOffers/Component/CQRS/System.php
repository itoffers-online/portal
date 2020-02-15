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

namespace ITOffers\Component\CQRS;

use ITOffers\Component\CQRS\System\Command;
use ITOffers\Component\CQRS\System\CommandBus;
use ITOffers\Component\CQRS\System\Queries;
use ITOffers\Component\CQRS\System\Query;
use ITOffers\Component\FeatureToggle\FeatureToggle;
use ITOffers\Offers\Application\Calendar;
use ITOffers\Offers\Application\Exception\Exception;
use Psr\Log\LoggerInterface;
use Throwable;

final class System
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var Queries
     */
    private $queries;

    /**
     * @var FeatureToggle
     */
    private $featureToggle;

    /**
     * @var EventStream
     */
    private $eventStream;

    /**
     * @var Calendar
     */
    private $calendar;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        CommandBus $commandBus,
        Queries $queries,
        FeatureToggle $featureToggle,
        Calendar $calendar,
        EventStream $eventStream,
        LoggerInterface $logger
    ) {
        $this->commandBus = $commandBus;
        $this->queries = $queries;
        $this->featureToggle = $featureToggle;
        $this->logger = $logger;
        $this->calendar = $calendar;
        $this->eventStream = $eventStream;
    }

    public function handle(Command $command) : void
    {
        if ($this->featureToggle->isDisabled($command)) {
            throw new Exception(\sprintf("Sorry, %s is currently disabled", $command->commandName()));
        }

        try {
            $this->commandBus->handle($command);
        } catch (Throwable $exception) {
            $this->logger->error(sprintf('Failed to handle command %s', \get_class($command)), [
                'system_time' => $this->calendar->currentTime()->format('c'),
                'exception' => \get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'trace' => $exception->getTraceAsString(),
            ]);

            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        try {
            $this->eventStream->flush();
        } catch (Throwable $exception) {
            $this->logger->error(sprintf('Failed to flush event stream after command %s', \get_class($command)), [
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

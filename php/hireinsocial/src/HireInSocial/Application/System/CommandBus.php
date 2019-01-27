<?php

declare(strict_types=1);

namespace HireInSocial\Application\System;

use HireInSocial\Application\Assertion;
use HireInSocial\Application\Exception\Exception;

final class CommandBus
{
    private $handlers;
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager, Handler ...$handlers)
    {
        foreach ($handlers as $handler) {
            Assertion::methodExists('__invoke', $handler, 'Can\'t register command handler without __invoke method.');

            $this->handlers[$handler->handles()] = $handler;
        }
        $this->transactionManager = $transactionManager;
    }

    public function handle(Command $command) : void
    {
        if (\array_key_exists($command->name(), $this->handlers)) {
            $this->transactionManager->begin();
            try {
                $this->handlers[$command->name()]($command);
                $this->transactionManager->commit();
            } catch (\Throwable $exception) {
                $this->transactionManager->rollback();
                throw $exception;
            }

        } else {
            throw new Exception(sprintf('Unknown command "%s"', $command->name()));
        }
    }
}

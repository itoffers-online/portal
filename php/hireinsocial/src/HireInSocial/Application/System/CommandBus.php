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
        if (\array_key_exists($command->commandName(), $this->handlers)) {
            $this->transactionManager->begin();

            try {
                $this->handlers[$command->commandName()]($command);
                $this->transactionManager->commit();
            } catch (\Throwable $exception) {
                $this->transactionManager->rollback();

                throw $exception;
            }
        } else {
            throw new Exception(sprintf('Unknown command "%s"', $command->commandName()));
        }
    }
}

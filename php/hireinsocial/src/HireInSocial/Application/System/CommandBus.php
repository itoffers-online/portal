<?php

declare(strict_types=1);

namespace HireInSocial\Application\System;

use HireInSocial\Application\Assertion;
use HireInSocial\Application\Exception\Exception;

final class CommandBus
{
    private $handlers;

    public function __construct(Handler ...$handlers)
    {
        foreach ($handlers as $handler) {
            Assertion::methodExists('__invoke', $handler, 'Can\'t register command handler without __invoke method.');

            $this->handlers[$handler->handles()] = $handler;
        }
    }

    public function handle(Command $command) : void
    {
        if (\array_key_exists($command->name(), $this->handlers)) {
            $this->handlers[$command->name()]($command);
        } else {
            throw new Exception(sprintf('Unknown command "%s"', $command->name()));
        }
    }
}

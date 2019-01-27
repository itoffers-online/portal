<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Unit\System;

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\System\Command;
use HireInSocial\Application\System\CommandBus;
use HireInSocial\Application\System\Handler;
use HireInSocial\Tests\Application\Double\Dummy\DummyTransactionManager;
use PHPUnit\Framework\TestCase;

final class CommandBusTest extends TestCase
{
    public function test_registering_command_without_invoke_method()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Can\'t register command handler without __invoke method.');

        new CommandBus(
            new DummyTransactionManager(),
            new class implements Handler {
                public function handles(): string
                {
                    return 'nothing';
                }
            }
        );
    }

    public function test_handling_unknown_command()
    {
        $commandBus = new CommandBus(
            new DummyTransactionManager(),
            new class implements Handler {
                public function __invoke()
                {
                }

                public function handles(): string
                {
                    return 'nothing';
                }
            }
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown command "FancyClassName"');

        $commandBus->handle(new class implements Command {
            public function name(): string
            {
                return 'FancyClassName';
            }
        });
    }

    public function test_handling_command()
    {
        $commandBus = new CommandBus(
            new DummyTransactionManager(),
            new class implements Handler {
                public function handles(): string
                {
                    return 'Command';
                }

                public function __invoke($command)
                {
                    $command->handle();
                }
            }
        );

        $command = new class implements Command {
            public $handled = false;

            public function name(): string
            {
                return 'Command';
            }

            public function handle(): void
            {
                $this->handled = true;
            }
        };

        $commandBus->handle($command);

        $this->assertTrue($command->handled);
    }
}

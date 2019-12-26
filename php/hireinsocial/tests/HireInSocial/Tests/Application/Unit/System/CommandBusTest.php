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

namespace HireInSocial\Tests\Application\Unit\System;

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\System\Command;
use HireInSocial\Application\System\CommandBus;
use HireInSocial\Application\System\Handler;
use HireInSocial\Tests\Application\Double\Dummy\DummyTransactionManager;
use PHPUnit\Framework\TestCase;

final class CommandBusTest extends TestCase
{
    public function test_registering_command_without_invoke_method() : void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Can\'t register command handler without __invoke method.');

        new CommandBus(
            new DummyTransactionManager(),
            new class implements Handler {
                public function handles() : string
                {
                    return 'nothing';
                }
            }
        );
    }

    public function test_handling_unknown_command() : void
    {
        $commandBus = new CommandBus(
            new DummyTransactionManager(),
            new class implements Handler {
                public function __invoke() : void
                {
                }

                public function handles() : string
                {
                    return 'nothing';
                }
            }
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown command "FancyClassName"');

        $commandBus->handle(new class implements Command {
            public function commandName() : string
            {
                return 'FancyClassName';
            }
        });
    }

    public function test_handling_command() : void
    {
        $commandBus = new CommandBus(
            new DummyTransactionManager(),
            new class implements Handler {
                public function handles() : string
                {
                    return 'Command';
                }

                public function __invoke($command) : void
                {
                    $command->handle();
                }
            }
        );

        $command = new class implements Command {
            public $handled = false;

            public function commandName() : string
            {
                return 'Command';
            }

            public function handle() : void
            {
                $this->handled = true;
            }
        };

        $commandBus->handle($command);

        $this->assertTrue($command->handled);
    }
}

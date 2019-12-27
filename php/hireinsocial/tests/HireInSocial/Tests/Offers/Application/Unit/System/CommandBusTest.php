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

namespace HireInSocial\Tests\Offers\Application\Unit\System;

use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\System\Command;
use HireInSocial\Offers\Application\System\CommandBus;
use HireInSocial\Offers\Application\System\Handler;
use HireInSocial\Tests\Offers\Application\Double\Dummy\DummyTransactionManager;
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
}

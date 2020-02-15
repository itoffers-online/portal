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

namespace ITOffers\Tests\Component\CQRS\Unit\System;

use ITOffers\Component\CQRS\System\Command;
use ITOffers\Component\CQRS\System\CommandBus;
use ITOffers\Component\CQRS\System\Handler;
use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Tests\Offers\Application\Double\Dummy\DummyTransactionManager;
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

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

namespace ITOffers\Tests\Component\CQRS\Unit;

use Aeon\Calendar\Gregorian\DateTime;
use Aeon\Calendar\Gregorian\GregorianCalendarStub;
use ITOffers\Component\CQRS\EventStream;
use ITOffers\Component\CQRS\EventStream\Event;
use ITOffers\Component\CQRS\Exception\Exception;
use ITOffers\Component\CQRS\System;
use ITOffers\Component\CQRS\System\CommandBus;
use ITOffers\Component\CQRS\System\Handler;
use ITOffers\Component\CQRS\System\Queries;
use ITOffers\Component\FeatureToggle\FeatureToggle;
use ITOffers\Tests\Component\CQRS\Double\Stub\EventStreamStub;
use ITOffers\Tests\Component\FeatureToggle\Double\Stub\DisabledFeatureStub;
use ITOffers\Tests\Offers\Application\Double\Dummy\DummyCommand;
use ITOffers\Tests\Offers\Application\Double\Dummy\DummyTransactionManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class SystemTest extends TestCase
{
    public function test_handling_command_disabled_by_feature_toggle() : void
    {
        $system = new System(
            new CommandBus(
                new DummyTransactionManager()
            ),
            new Queries(),
            new FeatureToggle(
                new DisabledFeatureStub(DummyCommand::class)
            ),
            new GregorianCalendarStub(),
            new EventStreamStub(),
            new NullLogger(),
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(\sprintf('Sorry, %s is currently disabled', DummyCommand::class));

        $system->handle(new DummyCommand());
    }

    public function test_flushing_event_stream_after_handing_command() : void
    {
        $eventStream = $this->createMock(EventStream::class);

        $eventStream->expects($this->once())
            ->method('record');

        $eventStream->expects($this->once())
            ->method('flush');

        $system = new System(
            new CommandBus(
                new DummyTransactionManager(),
                new class($eventStream) implements Handler {
                    private EventStream $eventStream;

                    public function __construct(EventStream $eventStream)
                    {
                        $this->eventStream = $eventStream;
                    }

                    public function handles() : string
                    {
                        return DummyCommand::class;
                    }

                    public function __invoke() : void
                    {
                        $this->eventStream->record(new class implements Event {
                            public function id() : UuidInterface
                            {
                                return Uuid::uuid4();
                            }

                            public function occurredAt() : DateTime
                            {
                                return DateTime::fromString('now');
                            }

                            public function payload() : array
                            {
                                return [];
                            }
                        });
                    }
                }
            ),
            new Queries(),
            new FeatureToggle(),
            new GregorianCalendarStub(),
            $eventStream,
            new NullLogger(),
        );

        $system->handle(new DummyCommand());
    }

    public function test_failed_event_stream_flush_after_handing_command() : void
    {
        $eventStream = $this->createMock(EventStream::class);

        $eventStream->expects($this->once())
            ->method('record');

        $eventStream->method('flush')
            ->willThrowException(new \RuntimeException('Can\'t Flush'));

        $system = new System(
            new CommandBus(
                new DummyTransactionManager(),
                new class($eventStream) implements Handler {
                    private EventStream $eventStream;

                    public function __construct(EventStream $eventStream)
                    {
                        $this->eventStream = $eventStream;
                    }

                    public function handles() : string
                    {
                        return DummyCommand::class;
                    }

                    public function __invoke() : void
                    {
                        $this->eventStream->record(new class implements Event {
                            public function id() : UuidInterface
                            {
                                return Uuid::uuid4();
                            }

                            public function occurredAt() : DateTime
                            {
                                return DateTime::fromString('now');
                            }

                            public function payload() : array
                            {
                                return [];
                            }
                        });
                    }
                }
            ),
            new Queries(),
            new FeatureToggle(),
            new GregorianCalendarStub(),
            $eventStream,
            new NullLogger()
        );

        $this->expectException(Exception::class);

        $system->handle(new DummyCommand());
    }
}

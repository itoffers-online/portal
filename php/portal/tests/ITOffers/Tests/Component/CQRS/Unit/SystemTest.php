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

use ITOffers\Component\CQRS\EventStream;
use ITOffers\Component\CQRS\Exception\Exception;
use ITOffers\Component\CQRS\System;
use ITOffers\Component\FeatureToggle\FeatureToggle;
use ITOffers\Tests\Component\Calendar\Double\Stub\CalendarStub;
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
            new System\CommandBus(
                new DummyTransactionManager()
            ),
            new System\Queries(),
            new FeatureToggle(
                new DisabledFeatureStub(DummyCommand::class)
            ),
            new CalendarStub(),
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
            new System\CommandBus(
                new DummyTransactionManager(),
                new class($eventStream) implements System\Handler {
                    /**
                     * @var EventStream
                     */
                    private $eventStream;

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
                        $this->eventStream->record(new class implements EventStream\Event {
                            public function id() : UuidInterface
                            {
                                return Uuid::uuid4();
                            }

                            public function occurredAt() : \DateTimeImmutable
                            {
                                return new \DateTimeImmutable();
                            }

                            public function payload() : array
                            {
                                return [];
                            }
                        });
                    }
                }
            ),
            new System\Queries(),
            new FeatureToggle(),
            new CalendarStub(),
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
            new System\CommandBus(
                new DummyTransactionManager(),
                new class($eventStream) implements System\Handler {
                    /**
                     * @var EventStream
                     */
                    private $eventStream;

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
                        $this->eventStream->record(new class implements EventStream\Event {
                            public function id() : UuidInterface
                            {
                                return Uuid::uuid4();
                            }

                            public function occurredAt() : \DateTimeImmutable
                            {
                                return new \DateTimeImmutable();
                            }

                            public function payload() : array
                            {
                                return [];
                            }
                        });
                    }
                }
            ),
            new System\Queries(),
            new FeatureToggle(),
            new CalendarStub(),
            $eventStream,
            new NullLogger()
        );

        $this->expectException(Exception::class);

        $system->handle(new DummyCommand());
    }
}

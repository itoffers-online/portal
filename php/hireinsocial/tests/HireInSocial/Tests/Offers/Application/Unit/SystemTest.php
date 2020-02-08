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

namespace HireInSocial\Tests\Offers\Application\Unit;

use HireInSocial\Offers\Application\EventStream;
use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\FeatureToggle;
use HireInSocial\Offers\Application\System;
use HireInSocial\Tests\Offers\Application\Double\Dummy\DummyCommand;
use HireInSocial\Tests\Offers\Application\Double\Dummy\DummyTransactionManager;
use HireInSocial\Tests\Offers\Application\Double\Stub\CalendarStub;
use HireInSocial\Tests\Offers\Application\Double\Stub\DisabledFeatureStub;
use HireInSocial\Tests\Offers\Application\Double\Stub\EventStreamStub;
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
        $eventStream = $this->getMockBuilder(EventStream::class)->getMock();

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
}

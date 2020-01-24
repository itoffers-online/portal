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

use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\FeatureToggle;
use HireInSocial\Offers\Application\System;
use HireInSocial\Tests\Offers\Application\Double\Dummy\DummyCommand;
use HireInSocial\Tests\Offers\Application\Double\Dummy\DummyTransactionManager;
use HireInSocial\Tests\Offers\Application\Double\Stub\CalendarStub;
use HireInSocial\Tests\Offers\Application\Double\Stub\DisabledFeatureStub;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

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
            new NullLogger(),
            new CalendarStub()
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(\sprintf('Sorry, %s is currently disabled', DummyCommand::class));

        $system->handle(new DummyCommand());
    }
}

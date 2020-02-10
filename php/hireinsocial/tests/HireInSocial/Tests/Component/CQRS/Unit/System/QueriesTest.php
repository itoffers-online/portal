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

namespace HireInSocial\Tests\Component\CQRS\Unit\System;

use HireInSocial\Component\CQRS\System\Queries;
use HireInSocial\Component\CQRS\System\Query;
use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Tests\Offers\Application\Double\Dummy\DummyQuery;
use HireInSocial\Tests\Offers\Application\Double\Fake\FakeQueryInterface;
use PHPUnit\Framework\TestCase;

final class QueriesTest extends TestCase
{
    public function test_accessing_queries() : void
    {
        $queries = new Queries();
        $queries->register(new DummyQuery());

        $this->assertInstanceOf(
            DummyQuery::class,
            $queries->get(DummyQuery::class)
        );
    }

    public function test_attempt_to_access_query_by_generic_query_interface() : void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Using generic Query interface in order to access specific query is impossible.');

        $queries = new Queries();
        $queries->get(Query::class);
    }

    public function test_accessing_not_registered_query() : void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Query "\DateTime" does not exists. Available Query: "\HireInSocial\Tests\Offers\Application\Double\Dummy\DummyQuery"');

        $queries = new Queries();
        $queries->register(new DummyQuery());

        $queries->get(\DateTime::class);
    }

    public function test_registering_two_implementations_of_the_same_query() : void
    {
        $query1 = new class implements FakeQueryInterface {
        };
        $query2 = new class implements FakeQueryInterface {
        };

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('Query %s that implements same interfaces as %s is already registered.', get_class($query1), get_class($query2)));

        $queries = new Queries();
        $queries->register($query1);
        $queries->register($query2);
    }
}

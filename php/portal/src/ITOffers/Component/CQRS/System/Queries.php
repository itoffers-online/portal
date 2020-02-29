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

namespace ITOffers\Component\CQRS\System;

use ITOffers\Offers\Application\Exception\Exception;

final class Queries
{
    private array

 $queries;

    public function __construct(Query ...$queries)
    {
        $this->queries = [];

        \array_map(
            function (Query $query) {
                $this->register($query);
            },
            $queries
        );
    }

    public function register(Query $query) : void
    {
        foreach ($this->queries as $queryRegistered) {
            if (\class_implements($queryRegistered) === \class_implements($query)) {
                throw new Exception(sprintf(
                    'Query %s that implements same interfaces as %s is already registered.',
                    \get_class($queryRegistered),
                    \get_class($query)
                ));
            }
        }

        $this->queries[\get_class($query)] = $query;
    }

    public function get(string $className) : Query
    {
        if ($className === Query::class) {
            throw new Exception('Using generic Query interface in order to access specific query is impossible.');
        }

        foreach ($this->queries as $query) {
            if ($query instanceof $className) {
                return $query;
            }
        }

        throw new Exception(sprintf('Query "\%s" does not exists. Available Query: "\%s"', $className, implode('", "', $this->availableQueries())));
    }

    public function availableQueries() : array
    {
        return array_keys($this->queries);
    }
}

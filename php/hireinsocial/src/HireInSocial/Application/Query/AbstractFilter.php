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

namespace HireInSocial\Application\Query;

use HireInSocial\Application\Assertion;
use HireInSocial\Application\Query\Filter\Column;

abstract class AbstractFilter
{
    protected $limit = 50;

    protected $offset = 0;

    protected $order;

    private $sortBy = [];

    public function changeSize(int $limit, int $offset) : self
    {
        Assertion::greaterOrEqualThan($offset, 0);
        Assertion::greaterThan($limit, 0);

        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    public function limit() : int
    {
        return $this->limit;
    }

    public function offset() : int
    {
        return $this->offset;
    }

    public function isSorted() : bool
    {
        return (bool) \count($this->sortBy);
    }

    public function addSortBy(Column $column) : self
    {
        Assertion::inArray($column->column(), $this->sortColumns());

        $this->sortBy[] = $column;

        return $this;
    }

    /**
     * @return Column[]
     */
    public function sortByColumns() : array
    {
        return $this->sortBy;
    }

    abstract protected function sortColumns() : array;
}

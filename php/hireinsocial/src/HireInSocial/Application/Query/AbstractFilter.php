<?php

declare (strict_types=1);

namespace HireInSocial\Application\Query;

use HireInSocial\Application\Assertion;

abstract class AbstractFilter
{
    protected $limit = 20;
    protected $offset = 0;
    protected $order;

    public function changeSlice(int $limit, int $offset) : self
    {
        Assertion::greaterThan($offset, 0);
        Assertion::greaterThan($limit, 0);

        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function offset(): int
    {
        return $this->offset;
    }
}
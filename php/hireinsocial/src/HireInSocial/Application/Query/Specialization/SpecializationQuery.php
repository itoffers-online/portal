<?php

declare (strict_types=1);

namespace HireInSocial\Application\Query\Specialization;

use HireInSocial\Application\Query\Specialization\Model\Specialization;
use HireInSocial\Application\System\Query;

interface SpecializationQuery extends Query
{
    /**
     * @return Specialization[]
     */
    public function all() : array;

    public function findBySlug(string $slug) : ?Specialization;
}
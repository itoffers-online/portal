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

namespace HireInSocial\Application\Query\Specialization;

use HireInSocial\Application\Query\Specialization\Model\Specialization;
use HireInSocial\Application\Query\Specialization\Model\Specializations;
use HireInSocial\Application\System\Query;

interface SpecializationQuery extends Query
{
    public function all() : Specializations;
    public function findBySlug(string $slug) : ?Specialization;
}

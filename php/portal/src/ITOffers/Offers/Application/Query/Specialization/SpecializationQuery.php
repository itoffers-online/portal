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

namespace ITOffers\Offers\Application\Query\Specialization;

use ITOffers\Component\CQRS\System\Query;
use ITOffers\Offers\Application\Query\Specialization\Model\Specialization;
use ITOffers\Offers\Application\Query\Specialization\Model\Specializations;

interface SpecializationQuery extends Query
{
    public function all() : Specializations;

    /**
     * @return array<string>
     */
    public function allSlugs() : array;

    public function findBySlug(string $slug) : ?Specialization;
}

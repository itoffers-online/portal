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

namespace HireInSocial\Application\Query\Offer;

use HireInSocial\Application\System\Query;

interface ApplicationQuery extends Query
{
    public function alreadyApplied(string $offerId, string $email) : bool;

    public function countFor(string $offerId) : int;
}

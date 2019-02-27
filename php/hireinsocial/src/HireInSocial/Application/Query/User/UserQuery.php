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

namespace HireInSocial\Application\Query\User;

use HireInSocial\Application\Query\User\Model\User;
use HireInSocial\Application\System\Query;

interface UserQuery extends Query
{
    public function findByFacebook(string $facebookUserAppId) : ?User;
}

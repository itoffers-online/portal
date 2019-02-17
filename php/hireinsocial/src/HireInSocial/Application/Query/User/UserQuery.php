<?php

declare(strict_types=1);

namespace HireInSocial\Application\Query\User;

use HireInSocial\Application\Query\User\Model\User;
use HireInSocial\Application\System\Query;

interface UserQuery extends Query
{
    public function findByFacebook(string $facebookUserAppId) : ?User;
}

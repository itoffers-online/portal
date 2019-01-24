<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Context;

use HireInSocial\Application\System;
use HireInSocial\Tests\Application\MotherObject\Command\Facebook\Page\PostToGroupMother;

final class SystemContext
{
    private $system;

    public function __construct(System $system)
    {
        $this->system = $system;
    }

    public function system() : System
    {
        return $this->system;
    }

    public function postToFacebookGroup(string $fbUserId) : void
    {
        $this->system->handle(PostToGroupMother::postAs($fbUserId));
    }
}

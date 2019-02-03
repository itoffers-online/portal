<?php

declare(strict_types=1);

namespace HireInSocial\Application\Specialization;

use HireInSocial\Application\Facebook\Group;
use HireInSocial\Application\Facebook\Page;

final class FacebookChannel
{
    private $page;
    private $group;

    public function __construct(Page $page, Group $group)
    {
        $this->page = $page;
        $this->group = $group;
    }

    public function page(): Page
    {
        return $this->page;
    }

    public function group(): Group
    {
        return $this->group;
    }
}

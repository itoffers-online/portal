<?php

declare(strict_types=1);

namespace HireInSocial\Application\Facebook;

interface Facebook
{
    public function postToGroupAsPage(Draft $post, Group $group, Page $page): string;
}

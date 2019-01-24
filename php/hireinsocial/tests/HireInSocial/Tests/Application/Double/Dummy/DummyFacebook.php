<?php

declare (strict_types=1);

namespace HireInSocial\Tests\Application\Double\Dummy;

use HireInSocial\Application\Facebook\Facebook;
use HireInSocial\Application\Facebook\Group;
use HireInSocial\Application\Facebook\Page;
use HireInSocial\Application\Facebook\Draft;

final class DummyFacebook implements Facebook
{
    public function userExists(string $facebookId): bool
    {
        return true;
    }

    public function postToGroupAsPage(Draft $post, Group $group, Page $page): string
    {
        return 'facebook_post_id_123456';
    }
}
<?php

declare (strict_types=1);

namespace HireInSocial\Application\Facebook;

interface Posts
{
    public function add(Post $post) : void;
}
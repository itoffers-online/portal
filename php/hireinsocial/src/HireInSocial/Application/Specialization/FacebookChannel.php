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

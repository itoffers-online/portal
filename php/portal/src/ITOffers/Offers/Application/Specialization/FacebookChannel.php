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

namespace ITOffers\Offers\Application\Specialization;

use ITOffers\Offers\Application\Facebook\Group;
use ITOffers\Offers\Application\Facebook\Page;

final class FacebookChannel
{
    private Page $page;

    private Group $group;

    public function __construct(Page $page, Group $group)
    {
        $this->page = $page;
        $this->group = $group;
    }

    public function page() : Page
    {
        return $this->page;
    }

    public function group() : Group
    {
        return $this->group;
    }
}

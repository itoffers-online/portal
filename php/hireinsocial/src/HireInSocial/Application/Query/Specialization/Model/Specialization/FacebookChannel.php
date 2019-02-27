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

namespace HireInSocial\Application\Query\Specialization\Model\Specialization;

final class FacebookChannel
{
    private $pageId;
    private $groupId;

    public function __construct(string $pageId, string $groupId)
    {
        $this->pageId = $pageId;
        $this->groupId = $groupId;
    }

    public function pageId(): string
    {
        return $this->pageId;
    }

    public function groupId(): string
    {
        return $this->groupId;
    }
}

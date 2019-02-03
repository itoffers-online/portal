<?php

declare(strict_types=1);

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

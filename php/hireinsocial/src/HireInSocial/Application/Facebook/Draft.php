<?php

declare(strict_types=1);

namespace HireInSocial\Application\Facebook;

final class Draft
{
    private $authorFbId;
    private $message;
    private $link;

    public function __construct(string $authorFbId, string $message, string $link)
    {
        $this->authorFbId = $authorFbId;
        $this->message = $message;
        $this->link = $link;
    }

    public function __toString() : string
    {
        return $this->message;
    }

    public function link(): string
    {
        return $this->link;
    }

    public function authorFbId(): string
    {
        return $this->authorFbId;
    }
}

<?php

declare(strict_types=1);

namespace HireInSocial\Application\Facebook;

final class Group
{
    /**
     * @var string
     */
    private $fbId;

    public function __construct(string $fbId)
    {
        $this->fbId = $fbId;
    }

    public function fbId(): string
    {
        return $this->fbId;
    }
}

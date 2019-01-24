<?php

declare(strict_types=1);

namespace HireInSocial\Application\Facebook;

final class Page
{
    private $fbId;
    private $accessToken;

    public function __construct(string $fbId, string $accessToken)
    {
        $this->fbId = $fbId;
        $this->accessToken = $accessToken;
    }

    public function fbId(): string
    {
        return $this->fbId;
    }

    public function accessToken(): string
    {
        return $this->accessToken;
    }
}

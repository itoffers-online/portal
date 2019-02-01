<?php

declare(strict_types=1);

namespace HireInSocial\Application\Facebook;

use HireInSocial\Application\Assertion;

final class Page
{
    private $fbId;
    private $accessToken;

    public function __construct(string $fbId, string $accessToken)
    {
        Assertion::betweenLength($fbId, 3, 255);
        Assertion::betweenLength($accessToken, 3, 255);

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

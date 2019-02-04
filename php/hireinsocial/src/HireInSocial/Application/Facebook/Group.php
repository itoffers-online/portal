<?php

declare(strict_types=1);

namespace HireInSocial\Application\Facebook;

use HireInSocial\Application\Assertion;

final class Group
{
    /**
     * @var string
     */
    private $fbId;

    public function __construct(string $fbId)
    {
        Assertion::betweenLength($fbId, 3, 255);

        $this->fbId = $fbId;
    }

    public function fbId(): string
    {
        return $this->fbId;
    }
}

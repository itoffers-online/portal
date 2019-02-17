<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\User;

use HireInSocial\Application\Command\ClassCommand;
use HireInSocial\Application\System\Command;

final class FacebookConnect implements Command
{
    use ClassCommand;

    /**
     * @var string
     */
    private $fbUserAppId;

    public function __construct(string $fbUserAppId)
    {
        $this->fbUserAppId = $fbUserAppId;
    }

    public function fbUserAppId(): string
    {
        return $this->fbUserAppId;
    }
}

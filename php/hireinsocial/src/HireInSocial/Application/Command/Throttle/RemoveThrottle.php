<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\Throttle;

use HireInSocial\Application\Command\ClassCommand;
use HireInSocial\Application\System\Command;

final class RemoveThrottle implements Command
{
    use ClassCommand;

    private $facebookUserId;

    public function __construct(string $facebookUserId)
    {
        $this->facebookUserId = $facebookUserId;
    }

    public function facebookUserId(): string
    {
        return $this->facebookUserId;
    }
}

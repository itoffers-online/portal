<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\Throttle;

use HireInSocial\Application\Offer\Throttle;
use HireInSocial\Application\System\Handler;

final class RemoveThrottleHandler implements Handler
{
    private $throttle;

    public function __construct(Throttle $throttle)
    {
        $this->throttle = $throttle;
    }

    public function handles(): string
    {
        return RemoveThrottle::class;
    }

    public function __invoke(RemoveThrottle $command) : void
    {
        $this->throttle->remove($command->facebookUserId());
    }
}

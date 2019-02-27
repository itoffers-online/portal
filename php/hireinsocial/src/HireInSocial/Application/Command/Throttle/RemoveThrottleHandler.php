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

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

namespace HireInSocial\Tests\Component\FeatureToggle\Double\Stub;

use HireInSocial\Component\CQRS\System\Command;
use HireInSocial\Component\FeatureToggle\Feature;

final class DisabledFeatureStub implements Feature
{
    /**
     * @var string
     */
    private $commandClass;

    public function __construct(string $commandClass)
    {
        $this->commandClass = $commandClass;
    }

    public function isEnabled() : bool
    {
        return false;
    }

    public function name() : string
    {
        return 'disabled_' . $this->commandClass;
    }

    public function isRelatedTo(Command $command) : bool
    {
        return \get_class($command) === $this->commandClass;
    }
}

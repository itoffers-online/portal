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

namespace HireInSocial\Offers\Application\FeatureToggle;

use HireInSocial\Offers\Application\System\Command;

interface Feature
{
    public function isEnabled() : bool;

    public function name() : string;

    public function isRelatedTo(Command $command) : bool;
}

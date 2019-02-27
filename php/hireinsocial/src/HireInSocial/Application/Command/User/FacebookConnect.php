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

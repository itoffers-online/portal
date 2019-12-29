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

namespace HireInSocial\Offers\Application\Command\User;

use HireInSocial\Offers\Application\Command\ClassCommand;
use HireInSocial\Offers\Application\System\Command;

final class FacebookConnect implements Command
{
    use ClassCommand;

    /**
     * @var string
     */
    private $fbUserAppId;

    /**
     * @var string
     */
    private $email;

    public function __construct(string $fbUserAppId, string $email)
    {
        $this->fbUserAppId = $fbUserAppId;
        $this->email = $email;
    }

    public function fbUserAppId() : string
    {
        return $this->fbUserAppId;
    }

    public function email() : string
    {
        return $this->email;
    }
}

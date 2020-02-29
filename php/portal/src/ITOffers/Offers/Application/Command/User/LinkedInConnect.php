<?php

declare(strict_types=1);

/*
 * This file is part of the itoffers.online project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ITOffers\Offers\Application\Command\User;

use ITOffers\Component\CQRS\System\Command;
use ITOffers\Offers\Application\Command\ClassCommand;

final class LinkedInConnect implements Command
{
    use ClassCommand;

    private string $userAppId;

    private string $email;

    public function __construct(string $userAppId, string $email)
    {
        $this->userAppId = $userAppId;
        $this->email = $email;
    }

    public function userAppId() : string
    {
        return $this->userAppId;
    }

    public function email() : string
    {
        return $this->email;
    }
}

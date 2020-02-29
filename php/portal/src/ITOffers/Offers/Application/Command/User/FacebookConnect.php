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

final class FacebookConnect implements Command
{
    use ClassCommand;

    private string $fbUserAppId;

    private string $email;

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

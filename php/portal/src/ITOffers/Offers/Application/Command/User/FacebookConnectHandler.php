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

use Aeon\Calendar\Gregorian\Calendar;
use ITOffers\Component\CQRS\System\Handler;
use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Offers\Application\User\User;
use ITOffers\Offers\Application\User\Users;

final class FacebookConnectHandler implements Handler
{
    private Users $users;

    private Calendar $calendar;

    public function __construct(Users $users, Calendar $calendar)
    {
        $this->users = $users;
        $this->calendar = $calendar;
    }

    public function handles() : string
    {
        return FacebookConnect::class;
    }

    public function __invoke(FacebookConnect $command) : void
    {
        try {
            $this->users->getByFB($command->fbUserAppId());
        } catch (Exception $e) {
            if ($this->users->emailExists($command->email())) {
                throw new Exception(\sprintf("Email %s already used by different account", $command->email()), 0, $e);
            }

            $this->users->add(User::fromFacebook($command->fbUserAppId(), $command->email(), $this->calendar));
        }
    }
}

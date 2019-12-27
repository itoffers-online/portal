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

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\System\Calendar;
use HireInSocial\Application\System\Handler;
use HireInSocial\Application\User\User;
use HireInSocial\Application\User\Users;

final class FacebookConnectHandler implements Handler
{
    /**
     * @var \HireInSocial\Application\User\Users
     */
    private $users;

    /**
     * @var \HireInSocial\Application\System\Calendar
     */
    private $calendar;

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
            $this->users->add(User::fromFacebook($command->fbUserAppId(), $this->calendar));
        }
    }
}

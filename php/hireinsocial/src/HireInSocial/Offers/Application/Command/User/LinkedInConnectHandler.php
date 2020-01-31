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

use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\System\Calendar;
use HireInSocial\Offers\Application\System\Handler;
use HireInSocial\Offers\Application\User\User;
use HireInSocial\Offers\Application\User\Users;

final class LinkedInConnectHandler implements Handler
{
    /**
     * @var Users
     */
    private $users;

    /**
     * @var Calendar
     */
    private $calendar;

    public function __construct(Users $users, Calendar $calendar)
    {
        $this->users = $users;
        $this->calendar = $calendar;
    }

    public function handles() : string
    {
        return LinkedInConnect::class;
    }

    public function __invoke(LinkedInConnect $command) : void
    {
        try {
            $this->users->getByLinkedIn($command->userAppId());
        } catch (Exception $e) {
            if ($this->users->emailExists($command->email())) {
                throw new Exception(\sprintf("Email %s already used by different account", $command->email()), 0, $e);
            }

            $this->users->add(User::fromLinkedIn($command->userAppId(), $command->email(), $this->calendar));
        }
    }
}

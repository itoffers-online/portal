<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\User;

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\System\Calendar;
use HireInSocial\Application\System\Handler;
use HireInSocial\Application\User\User;
use HireInSocial\Application\User\Users;

final class FacebookConnectHandler implements Handler
{
    private $users;
    private $calendar;

    public function __construct(Users $users, Calendar $calendar)
    {
        $this->users = $users;
        $this->calendar = $calendar;
    }

    public function handles(): string
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

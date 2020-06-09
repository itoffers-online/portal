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
use ITOffers\Offers\Application\User\Users;
use Ramsey\Uuid\Uuid;

final class BlockUserHandler implements Handler
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
        return BlockUser::class;
    }

    public function __invoke(BlockUser $command) : void
    {
        $this->users->getById(Uuid::fromString($command->getUserId()))
            ->block($this->calendar);
    }
}

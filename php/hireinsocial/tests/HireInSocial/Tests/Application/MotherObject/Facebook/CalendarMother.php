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

namespace HireInSocial\Tests\Application\MotherObject\Facebook;

use HireInSocial\Infrastructure\PHP\SystemCalendar\SystemCalendar;
use HireInSocial\Application\System\Calendar;

final class CalendarMother
{
    public static function utc() : Calendar
    {
        return new SystemCalendar(new \DateTimeZone('UTC'));
    }
}

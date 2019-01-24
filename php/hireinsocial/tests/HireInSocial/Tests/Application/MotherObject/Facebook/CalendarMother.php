<?php

declare(strict_types=1);

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

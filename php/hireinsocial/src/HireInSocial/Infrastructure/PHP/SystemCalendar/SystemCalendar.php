<?php

declare (strict_types=1);

namespace HireInSocial\Infrastructure\PHP\SystemCalendar;

use HireInSocial\Application\System\Calendar;

final class SystemCalendar implements Calendar
{
    private $timeZone;

    public function __construct(\DateTimeZone $timeZone)
    {
        $this->timeZone = $timeZone;
    }

    public function currentTime(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', $this->timeZone);
    }
}
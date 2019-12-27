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

namespace HireInSocial\Offers\Infrastructure\PHP\SystemCalendar;

use HireInSocial\Offers\Application\System\Calendar;

final class SystemCalendar implements Calendar
{
    /**
     * @var \DateTimeZone
     */
    private $timeZone;

    public function __construct(\DateTimeZone $timeZone)
    {
        $this->timeZone = $timeZone;
    }

    public function currentTime() : \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', $this->timeZone);
    }
}

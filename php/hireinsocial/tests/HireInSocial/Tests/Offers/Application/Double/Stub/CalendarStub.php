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

namespace HireInSocial\Tests\Offers\Application\Double\Stub;

use HireInSocial\Offers\Application\System\Calendar;

class CalendarStub implements Calendar
{
    /**
     * @var \DateTimeImmutable
     */
    private $currentTime;

    public function __construct(\DateTimeImmutable $currentTime = null)
    {
        $this->currentTime = $currentTime ? $currentTime : new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function currentTime() : \DateTimeImmutable
    {
        return $this->currentTime;
    }

    public function goBack(int $seconds) : void
    {
        $this->currentTime = $this->currentTime->modify(sprintf('-%d seconds', $seconds));
    }

    public function addDays(int $days) : void
    {
        $this->currentTime = $this->currentTime->modify(sprintf('+%d days', $days));
    }
}

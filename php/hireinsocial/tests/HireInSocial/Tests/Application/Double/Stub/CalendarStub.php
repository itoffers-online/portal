<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Double\Stub;

use HireInSocial\Application\System\Calendar;

class CalendarStub implements Calendar
{
    /**
     * @var \DateTimeImmutable
     */
    private $currentTime;

    public function __construct(\DateTimeImmutable $currentTime)
    {
        $this->currentTime = $currentTime;
    }

    public function currentTime(): \DateTimeImmutable
    {
        return $this->currentTime;
    }

    public function goBack(int $seconds) : void
    {
        $this->currentTime = $this->currentTime->modify(sprintf('-%d seconds', $seconds));
    }
}

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

namespace ITOffers\Offers\Application\Offer;

use Aeon\Calendar\TimeUnit;
use Aeon\Calendar\Gregorian\Calendar;
use ITOffers\Offers\Application\User\User;

final class Throttling
{
    public const LIMIT = 2;

    public const SINCE_DAYS = 14;

    private int $limit;

    private TimeUnit $since;

    private Calendar $calendar;

    public function __construct(int $defaultLimit, TimeUnit $since, Calendar $calendar)
    {
        $this->limit = $defaultLimit;
        $this->since = $since;
        $this->calendar = $calendar;
    }

    public static function createDefault(Calendar $calendar) : self
    {
        return new Throttling(
            self::LIMIT,
            TimeUnit::days(self::SINCE_DAYS),
            $calendar
        );
    }

    public function limit() : int
    {
        return $this->limit;
    }

    public function since() : TimeUnit
    {
        return $this->since;
    }

    public function isThrottled(User $user, Offers $offers) : bool
    {
        if ($offers->postedBy($user, $this->calendar->now()->sub($this->since))->count() >= $this->limit) {
            return true;
        }

        return false;
    }
}

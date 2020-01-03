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

namespace HireInSocial\Offers\Application\Offer\Salary;

use HireInSocial\Offers\Application\Assertion;

final class Period
{
    private const HOUR = 'HOUR';

    private const DAY = 'DAY';

    private const WEEK = 'WEEK';

    private const MONTH = 'MONTH';

    private const YEAR = 'YEAR';

    private const IN_TOTAL = 'IN_TOTAL';

    /**
     * @var string
     */
    private $type;

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public static function fromString(string $type) : self
    {
        Assertion::inArray($type, [self::HOUR, self::DAY, self::WEEK, self::MONTH, self::YEAR, self::IN_TOTAL]);

        return new self($type);
    }

    public static function perHour() : self
    {
        return new self(self::HOUR);
    }

    public static function perDay() : self
    {
        return new self(self::DAY);
    }

    public static function perWeek() : self
    {
        return new self(self::WEEK);
    }

    public static function perMonth() : self
    {
        return new self(self::MONTH);
    }

    public static function perYear() : self
    {
        return new self(self::YEAR);
    }

    public static function inTotal() : self
    {
        return new self(self::IN_TOTAL);
    }

    public function isTotal() : bool
    {
        return $this->type === self::IN_TOTAL;
    }

    public function type() : string
    {
        return $this->type;
    }
}

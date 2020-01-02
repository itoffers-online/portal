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

namespace HireInSocial\Offers\Application\Query\Offer;

use HireInSocial\Offers\Application\Assertion;
use HireInSocial\Offers\Application\Query\AbstractFilter;
use HireInSocial\Offers\Application\Query\Filter\Column;

final class OfferFilter extends AbstractFilter
{
    public const COLUMN_SALARY = 'salary';

    public const COLUMN_CREATED_AT = 'created_at';

    public const SORT_SALARY_ASC = 'salary_asc';

    public const SORT_SALARY_DESC = 'salary_desc';

    public const SORT_CREATED_AT_ASC = 'created_at_asc';

    public const SORT_CREATED_AT_DESC = 'created_at_desc';

    /**
     * @var string|null
     */
    private $specialization;

    /**
     * @var \DateTimeImmutable
     */
    private $sinceDate;

    /**
     * @var \DateTimeImmutable|null
     */
    private $tillDate;

    /**
     * @var bool|null
     */
    private $remote;

    /**
     * @var bool|null
     */
    private $withSalary;

    /**
     * @var string|null
     */
    private $userId;

    /**
     * @var string|null
     */
    private $afterOffer;

    /**
     * @var int|null
     */
    private $seniorityLevel;

    private function __construct()
    {
        $this->newerThan(new \DateTimeImmutable('-2 weeks', new \DateTimeZone('UTC')));
    }

    public static function allFor(string $specialization) : self
    {
        $filter = new self();
        $filter->specialization = $specialization;

        return $filter;
    }

    public static function all() : self
    {
        return new self();
    }

    public function newerThan(\DateTimeImmutable $dateTime) : self
    {
        Assertion::eq($dateTime->getTimezone(), new \DateTimeZone('UTC'), 'Timezone UTC is required for filter.');

        $this->sinceDate = $dateTime;

        return $this;
    }

    public function olderThan(\DateTimeImmutable $dateTime) : self
    {
        Assertion::eq($dateTime->getTimezone(), new \DateTimeZone('UTC'), 'Timezone UTC is required for filter.');

        $this->tillDate = $dateTime;

        return $this;
    }

    public function showAfter(string $offerId) : self
    {
        $this->afterOffer = $offerId;

        return $this;
    }

    public function sinceDate() : \DateTimeImmutable
    {
        return $this->sinceDate;
    }

    public function tillDate() : ?\DateTimeImmutable
    {
        return $this->tillDate;
    }

    public function specialization() : ?string
    {
        return $this->specialization;
    }

    public function onlyRemote() : self
    {
        $this->remote = true;

        return $this;
    }

    public function remote() : ?bool
    {
        return $this->remote;
    }

    public function onlyWithSalary() : self
    {
        $this->withSalary = true;

        return $this;
    }

    public function withSalary() : ?bool
    {
        return $this->withSalary;
    }

    public function belongsTo(string $userId) : self
    {
        $this->userId = $userId;

        return $this;
    }

    public function onlyFor(int $seniorityLevel) : self
    {
        $this->seniorityLevel = $seniorityLevel;

        return $this;
    }

    /**
     * @return int|null
     */
    public function seniorityLevel() : ?int
    {
        return $this->seniorityLevel;
    }

    public function userId() : ?string
    {
        return $this->userId;
    }

    public function afterOfferId() : ?string
    {
        return $this->afterOffer;
    }

    public function sortBy(string $columnType) : self
    {
        switch ($columnType) {
            case self::SORT_CREATED_AT_DESC:
                $this->addSortBy(Column::desc(self::COLUMN_CREATED_AT));

                break;
            case self::SORT_CREATED_AT_ASC:
                $this->addSortBy(Column::asc(self::COLUMN_CREATED_AT));

                break;
            case self::SORT_SALARY_DESC:
                $this->addSortBy(Column::desc(self::COLUMN_SALARY));

                break;
            case self::SORT_SALARY_ASC:
                $this->addSortBy(Column::asc(self::COLUMN_SALARY));

                break;
        }

        return $this;
    }

    protected function sortColumns() : array
    {
        return [
            self::COLUMN_SALARY,
            self::COLUMN_CREATED_AT,
        ];
    }
}

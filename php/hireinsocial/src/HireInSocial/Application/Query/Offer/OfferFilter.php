<?php

declare(strict_types=1);

namespace HireInSocial\Application\Query\Offer;

use HireInSocial\Application\Assertion;
use HireInSocial\Application\Query\AbstractFilter;

final class OfferFilter extends AbstractFilter
{
    /**
     * @var string
     */
    private $specialization;
    private $sinceDate;
    private $tillDate;

    private function __construct()
    {
        $this->newerThan(new \DateTimeImmutable('-2 weeks', new \DateTimeZone('UTC')));
        $this->olderThan(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
    }

    public static function allFor(string $specialization) : self
    {
        $filter = new self();
        $filter->specialization = $specialization;

        return $filter;
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

    public function sinceDate(): \DateTimeImmutable
    {
        return $this->sinceDate;
    }

    public function tillDate(): \DateTimeImmutable
    {
        return $this->tillDate;
    }

    public function specialization(): string
    {
        return $this->specialization;
    }
}

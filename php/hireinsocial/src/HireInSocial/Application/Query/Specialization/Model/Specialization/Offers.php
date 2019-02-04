<?php

declare(strict_types=1);

namespace HireInSocial\Application\Query\Specialization\Model\Specialization;

final class Offers
{
    private $count;
    private $latestOfferDate;

    private function __construct()
    {
        $this->count = 0;
    }

    public static function create(int $count, \DateTimeImmutable $latestOfferDate)
    {
        $specialization = new self();
        $specialization->count = $count;
        $specialization->latestOfferDate = $latestOfferDate;

        return $specialization;
    }


    public static function noOffers()
    {
        return new self();
    }

    public function count(): int
    {
        return $this->count;
    }

    public function latestOfferDate(): ?\DateTimeImmutable
    {
        return $this->latestOfferDate;
    }
}

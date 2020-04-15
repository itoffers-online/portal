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

namespace ITOffers\Offers\Application\Query\Specialization\Model\Specialization;

final class Offers
{
    private int $count;

    private ?\DateTimeImmutable $latestOfferDate = null;

    private function __construct()
    {
        $this->count = 0;
    }

    public static function create(int $count, \DateTimeImmutable $latestOfferDate) : self
    {
        $specialization = new self();
        $specialization->count = $count;
        $specialization->latestOfferDate = $latestOfferDate;

        return $specialization;
    }

    public static function noOffers() : self
    {
        return new self();
    }

    public function count() : int
    {
        return $this->count;
    }

    public function latestOfferDate() : ?\DateTimeImmutable
    {
        return $this->latestOfferDate;
    }
}

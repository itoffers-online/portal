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

namespace ITOffers\Offers\UserInterface;

use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Offers\Application\Query\Offer\Model\Offer;
use ITOffers\Offers\Application\Query\Offer\Model\Offer\Location;
use ITOffers\Offers\Application\Query\Offer\Model\Offer\Salary;
use ITOffers\Offers\Offers;
use ITOffers\Offers\UserInterface\Country\Countries;
use Stidges\CountryFlags\CountryFlag;

final class OfferExtension
{
    private string $locale;

    private Offers $offers;

    public function __construct(string $locale, Offers $offers)
    {
        $this->locale = $locale;
        $this->offers = $offers;
    }

    public function salaryInteger(int $amount) : string
    {
        return (\NumberFormatter::create($this->locale, \NumberFormatter::DEFAULT_STYLE))->format($amount);
    }

    public function salaryType(Salary $salary) : string
    {
        if ($salary->isNet()) {
            return 'net ' . ($salary->periodTypeTotal() ? ' in total' : 'per ' . \mb_strtolower($salary->periodType()));
        }

        return 'gross ' . ($salary->periodTypeTotal() ? ' in total' : 'per ' . \mb_strtolower($salary->periodType()));
    }

    public function locationText(Location $location) : string
    {
        if ($location->isRemote()) {
            return 'Remote';
        }

        return \sprintf("%s, %s", $this->locationCountryName($location->countryCode()), $location->city());
    }

    public function salaryIntegerShort(int $amount) : string
    {
        return (new MetricSuffix($amount, $this->locale))->convert();
    }

    public function workType(Location $location) : string
    {
        if ($location->isAtOffice()) {
            return 'At Office';
        }

        if ($location->isPartiallyRemote()) {
            return 'Partially Remote';
        }

        return 'Remote';
    }

    public function seniorityLevelName(int $level) : string
    {
        switch ($level) {
            case 0:
                return 'Intern';
            case 1:
                return 'Junior';
            case 2:
                return 'Mid';
            case 3:
                return 'Senior';
            case 4:
                return 'Expert';
            default:
                throw new Exception("Unknown seniority level");
        }
    }

    public static function seniorityLevelFromName(string $name) : int
    {
        switch (\mb_strtolower($name)) {
            case 'intern':
                return 0;
            case 'junior':
                return 1;
            case 'mid':
                return 2;
            case 'senior':
                return 3;
            case 'expert':
                return 4;
            default:
                throw new Exception("Unknown seniority level");
        }
    }

    public function locationCountryFlag(string $countryCode) : string
    {
        try {
            return (new CountryFlag)->get($countryCode);
        } catch (\Throwable $e) {
            return $countryCode;
        }
    }

    public function localeCountryFlag(string $locale) : string
    {
        try {
            return (new CountryFlag)->get(\mb_substr($locale, 3, 5));
        } catch (\Throwable $e) {
            return $locale;
        }
    }

    public function locationCountryName(string $countryCode) : string
    {
        return Countries::name($countryCode);
    }

    public function olderThan(Offer $offer, int $days) : bool
    {
        return $offer->createdAt()->diff(new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->days >= $days;
    }

    public function autoRenewsLeft(Offer $offer) : int
    {
        return $this->offers->offerAutoRenewQuery()->countRenewsLeft($offer->id()->toString());
    }

    public function usedAutoRenews(Offer $offer) : int
    {
        return $this->offers->offerAutoRenewQuery()->countUsedRenews($offer->id()->toString());
    }

    public function totalAutoRenews(Offer $offer) : int
    {
        return $this->offers->offerAutoRenewQuery()->countTotalRenews($offer->id()->toString());
    }
}

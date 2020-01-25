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

namespace App\Offers\Twig\Extension;

use HireInSocial\Offers\UserInterface\OfferExtension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class TwigOfferExtension extends AbstractExtension
{
    /**
     * @var OfferExtension
     */
    private $extension;

    public function __construct(OfferExtension $extension)
    {
        $this->extension = $extension;
    }

    public function getFilters() : array
    {
        return [
            new TwigFilter('offer_seniority_level_name', [$this->extension, 'seniorityLevelName']),
            new TwigFilter('offer_salary_integer', [$this->extension, 'salaryInteger']),
            new TwigFilter('offer_salary_integer_short', [$this->extension, 'salaryIntegerShort']),
            new TwigFilter('offer_locale_country_flag', [$this->extension, 'localeCountryFlag']),
            new TwigFilter('offer_location_country_flag', [$this->extension, 'locationCountryFlag']),
            new TwigFilter('offer_location_country_name', [$this->extension, 'locationCountryName']),
            new TwigFilter('offer_older_than', [$this->extension, 'olderThan']),
        ];
    }
}

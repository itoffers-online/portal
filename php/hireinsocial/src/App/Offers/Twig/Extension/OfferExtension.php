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

use HireInSocial\Offers\Application\Exception\Exception;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class OfferExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public function getFilters() : array
    {
        return [
            new TwigFilter('offer_seniority_level_name', [$this, 'seniorityLevelName']),
            new TwigFilter('offer_salary_integer', [$this, 'salaryInteger']),
        ];
    }

    public function salaryInteger(int $amount) : string
    {
        return (\NumberFormatter::create($this->locale, \NumberFormatter::DEFAULT_STYLE))->format($amount);
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
}

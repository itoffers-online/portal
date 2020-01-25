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

use HireInSocial\Offers\UserInterface\SpecializationExtension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class TwigSpecializationExtension extends AbstractExtension
{
    /**
     * @var SpecializationExtension
     */
    private $extension;

    public function __construct(SpecializationExtension $extension)
    {
        $this->extension = $extension;
    }

    public function getFilters() : array
    {
        return [
            new TwigFilter('specialization_name', [$this->extension, 'name']),
            new TwigFilter('specialization_title', [$this->extension, 'title']),
        ];
    }
}

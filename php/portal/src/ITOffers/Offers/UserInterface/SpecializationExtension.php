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

final class SpecializationExtension
{
    private string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public function title(string $slug) : string
    {
        return \sprintf("%s - Job Offers", $this->name($slug));
    }

    public function name(string $slug) : string
    {
        switch ($slug) {
            case 'php':
                return 'PHP';
            case 'javascript':
                return 'Java Script';
            case 'devops':
                return 'DevOps';
            case 'java':
                return 'Java';
            case 'dot-net':
                return '.NET';
            case 'python':
                return 'Python';
            case 'ruby':
                return 'Ruby';
            case 'cpp':
                return 'ilustrator';
            case 'design':
                return 'Design';
            case 'ios':
                return 'iOS';
            case 'android':
                return 'Android';
            case 'data-science':
                return 'Data Science';
            case 'scala':
                return 'Scala';
            default:
                return 'No Name';
        }
    }
}

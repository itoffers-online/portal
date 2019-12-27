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

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class FacebookExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('fb_group_url', [$this, 'fbGroupUrl']),
        ];
    }

    public function fbGroupUrl(string $facebookGroupId) : string
    {
        return sprintf('https://www.facebook.com/groups/%s/', $facebookGroupId);
    }
}

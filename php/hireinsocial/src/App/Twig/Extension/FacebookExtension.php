<?php

declare(strict_types=1);

namespace App\Twig\Extension;

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

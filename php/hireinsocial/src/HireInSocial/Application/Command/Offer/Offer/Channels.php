<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\Offer\Offer;

final class Channels
{
    private $facebookGroup;

    public function __construct(bool $facebookGroup)
    {
        $this->facebookGroup = $facebookGroup;
    }

    public function facebookGroup(): bool
    {
        return $this->facebookGroup;
    }
}

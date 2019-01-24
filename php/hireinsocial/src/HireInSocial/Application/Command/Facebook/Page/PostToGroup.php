<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\Facebook\Page;

use HireInSocial\Application\Command\ClassCommand;
use HireInSocial\Application\Command\Offer\Offer;
use HireInSocial\Application\System\Command;

final class PostToGroup implements Command
{
    use ClassCommand;

    private $fbUserId;
    private $offer;

    public function __construct(
        string $fbUserId,
        Offer $offer
    ) {
        $this->fbUserId = $fbUserId;
        $this->offer = $offer;
    }

    public function fbUserId(): string
    {
        return $this->fbUserId;
    }

    public function offer(): Offer
    {
        return $this->offer;
    }
}

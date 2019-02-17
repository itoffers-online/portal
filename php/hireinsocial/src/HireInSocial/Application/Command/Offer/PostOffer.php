<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\Offer;

use HireInSocial\Application\Command\ClassCommand;
use HireInSocial\Application\Command\Offer\Offer\Offer;
use HireInSocial\Application\System\Command;

final class PostOffer implements Command
{
    use ClassCommand;

    private $specialization;
    private $userId;
    private $offer;

    public function __construct(
        string $specialization,
        string $userId,
        Offer $offer
    ) {
        $this->userId = $userId;
        $this->offer = $offer;
        $this->specialization = $specialization;
    }

    public function specialization(): string
    {
        return $this->specialization;
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function offer(): Offer
    {
        return $this->offer;
    }
}

<?php

declare(strict_types=1);

namespace HireInSocial\Application\Facebook;

use HireInSocial\Application\Offer\Offer;
use Ramsey\Uuid\UuidInterface;

class Post
{
    private $fbId;
    private $jobOfferId;

    public function __construct(string $fbId, Offer $offer)
    {
        $this->fbId = $fbId;
        $this->jobOfferId = $offer->id();
    }

    public function fbId(): string
    {
        return $this->fbId;
    }

    public function jobOfferId(): UuidInterface
    {
        return $this->jobOfferId;
    }
}

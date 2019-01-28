<?php

declare(strict_types=1);

namespace HireInSocial\Application\Facebook;

use HireInSocial\Application\Offer\Offer;
use Ramsey\Uuid\UuidInterface;

class Post
{
    private $fbId;
    private $jobOfferId;
    private $fbAuthorId;

    public function __construct(string $fbId, Offer $offer, Draft $post)
    {
        $this->fbId = $fbId;
        $this->jobOfferId = $offer->id();
        $this->fbAuthorId = $post->authorFbId();
    }

    public function fbId(): string
    {
        return $this->fbId;
    }

    public function authorId() : string
    {
        return $this->fbAuthorId;
    }

    public function jobOfferId(): UuidInterface
    {
        return $this->jobOfferId;
    }
}

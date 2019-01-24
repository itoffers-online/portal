<?php

declare(strict_types=1);

namespace HireInSocial\Application\Facebook;

use HireInSocial\Application\Offer\Offer;
use Ramsey\Uuid\UuidInterface;

final class Post
{
    private $fbId;
    private $jobOfferId;
    private $post;

    public function __construct(string $fbId, Offer $offer, Draft $post)
    {
        $this->fbId = $fbId;
        $this->jobOfferId = $offer->id();
        $this->post = $post;
    }

    public function fbId(): string
    {
        return $this->fbId;
    }

    public function authorId() : string
    {
        return $this->post->authorFbId();
    }

    public function jobOfferId(): UuidInterface
    {
        return $this->jobOfferId;
    }
}

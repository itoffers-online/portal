<?php

declare(strict_types=1);

namespace HireInSocial\Application\Specialization;

use HireInSocial\Application\Assertion;
use HireInSocial\Application\Facebook\Group;
use HireInSocial\Application\Facebook\Page;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Specialization
{
    private $id;
    private $slug;
    private $facebookChannelPageId;
    private $facebookChannelPageAccessToken;
    private $facebookChannelGroupId;

    public function __construct(string $slug)
    {
        Assertion::regex(\mb_strtolower($slug), '/^[a-z\-\_]+$/');
        Assertion::betweenLength($slug, 3, 255);

        $this->id = (string) Uuid::uuid4();
        $this->slug = \mb_strtolower($slug);
    }

    public function id(): UuidInterface
    {
        return Uuid::fromString($this->id);
    }

    public function slug() : string
    {
        return $this->slug;
    }

    public function is(string $slug) : bool
    {
        return $this->slug === \mb_strtolower($slug);
    }

    public function setFacebook(FacebookChannel $facebookChannel) : void
    {
        $this->facebookChannelPageId = $facebookChannel->page()->fbId();
        $this->facebookChannelPageAccessToken = $facebookChannel->page()->accessToken();
        $this->facebookChannelGroupId = $facebookChannel->group()->fbId();
    }

    public function removeFacebook() : void
    {
        $this->facebookChannelPageId = null;
        $this->facebookChannelPageAccessToken = null;
        $this->facebookChannelGroupId = null;
    }

    public function facebookChannel(): ?FacebookChannel
    {
        if (!\is_null($this->facebookChannelPageId) &&
            !\is_null($this->facebookChannelPageAccessToken) &&
            !\is_null($this->facebookChannelGroupId)
        ) {
            return new FacebookChannel(
                new Page(
                    $this->facebookChannelPageId,
                    $this->facebookChannelPageAccessToken
                ),
                new Group(
                    $this->facebookChannelGroupId
                )
            );
        }

        return null;
    }
}

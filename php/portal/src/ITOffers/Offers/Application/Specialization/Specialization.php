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

namespace ITOffers\Offers\Application\Specialization;

use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\Facebook\Group;
use ITOffers\Offers\Application\Facebook\Page;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Specialization
{
    private string $id;

    private string $slug;

    private ?string $facebookChannelPageId = null;

    private ?string $facebookChannelPageAccessToken = null;

    private ?string $facebookChannelGroupId = null;

    private ?string $facebookChannelGroupName = null;

    private ?string $twitterAccountId = null;

    private ?string $twitterScreenName = null;

    private ?string $twitteroAuthToken = null;

    private ?string $twitteroAuthTokenSecret = null;

    public function __construct(string $slug)
    {
        Assertion::regex(\mb_strtolower($slug), '/^[a-z\-\_]+$/');
        Assertion::betweenLength($slug, 3, 255);

        $this->id = Uuid::uuid4()->toString();
        $this->slug = \mb_strtolower($slug);
    }

    public function id() : UuidInterface
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
        $this->facebookChannelGroupName = $facebookChannel->group()->name();
    }

    public function setTwitter(TwitterChannel $twitterChannel) : void
    {
        $this->twitterAccountId = $twitterChannel->accountId();
        $this->twitterScreenName = $twitterChannel->screenName();
        $this->twitteroAuthToken = $twitterChannel->oauthToken();
        $this->twitteroAuthTokenSecret = $twitterChannel->oauthTokenSecret();
    }

    public function removeFacebook() : void
    {
        $this->facebookChannelPageId = null;
        $this->facebookChannelPageAccessToken = null;
        $this->facebookChannelGroupId = null;
    }

    public function removeTwitter() : void
    {
        $this->twitterAccountId = null;
        $this->twitterScreenName = null;
        $this->twitteroAuthToken = null;
        $this->twitteroAuthTokenSecret = null;
    }

    public function twitterChannel() : ?TwitterChannel
    {
        if (!\is_null($this->twitterAccountId) &&
            !\is_null($this->twitterScreenName) &&
            !\is_null($this->twitteroAuthToken) &&
            !\is_null($this->twitteroAuthTokenSecret)) {
            return new TwitterChannel(
                $this->twitterAccountId,
                $this->twitterScreenName,
                $this->twitteroAuthToken,
                $this->twitteroAuthTokenSecret
            );
        }

        return null;
    }

    public function facebookChannel() : ?FacebookChannel
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
                    $this->facebookChannelGroupId,
                    $this->facebookChannelGroupName
                )
            );
        }

        return null;
    }
}

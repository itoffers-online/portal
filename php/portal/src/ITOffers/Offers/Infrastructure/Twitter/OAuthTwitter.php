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

namespace ITOffers\Offers\Infrastructure\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\Specialization\TwitterChannel;
use ITOffers\Offers\Application\Twitter\Twitter;

final class OAuthTwitter implements Twitter
{
    private string $apiKey;

    private string $apiSecret;

    public function __construct(string $apiKey, string $apiSecret)
    {
        Assertion::notEmpty($apiKey);
        Assertion::notEmpty($apiSecret);

        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public function tweet(string $message, TwitterChannel $account) : string
    {
        $connection = new TwitterOAuth(
            $this->apiKey,
            $this->apiSecret,
            $account->oauthToken(),
            $account->oauthTokenSecret()
        );

        /** @var \stdClass $result */
        $result = $connection->post('statuses/update', [
            'status' => $message,
        ]);

        return $result->id_str;
    }
}

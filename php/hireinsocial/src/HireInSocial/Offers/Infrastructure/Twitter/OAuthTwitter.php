<?php

declare(strict_types=1);

/*
 * This file is part of the Hire in Social project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HireInSocial\Offers\Infrastructure\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use HireInSocial\Offers\Application\Assertion;
use HireInSocial\Offers\Application\Specialization\TwitterChannel;
use HireInSocial\Offers\Application\Twitter\Twitter;

final class OAuthTwitter implements Twitter
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiSecret;

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

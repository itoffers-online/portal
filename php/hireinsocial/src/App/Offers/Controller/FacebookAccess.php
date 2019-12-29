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

namespace App\Offers\Controller;

use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Psr\Log\LoggerInterface;

trait FacebookAccess
{
    /**
     * @param Facebook $facebook
     * @param AccessToken $accessToken
     * @param LoggerInterface $logger
     * @return array{id: string, email: string}
     * @throws FacebookSDKException
     */
    public function getFbUser(Facebook $facebook, AccessToken $accessToken, LoggerInterface $logger) : array
    {
        $facebookResponse = $facebook->get('me?fields=email, name', $accessToken);

        $logger->debug('acebook /me response', ['body' => $facebookResponse->getBody()]);

        return [
            'id' => $facebookResponse->getDecodedBody()['id'],
            'email' => \array_key_exists('email', $facebookResponse->getDecodedBody()) ? $facebookResponse->getDecodedBody()['email'] : null,
        ];
    }

    public function clearFbPermissions(Facebook $facebook, string $fbUserId, AccessToken $accessToken, LoggerInterface $logger) : void
    {
        $facebookResponse = $facebook->delete(\sprintf('%s/permissions', $fbUserId), [], $accessToken);

        $logger->debug('Facebook DELETE permissions response', ['body' => $facebookResponse->getBody()]);
    }
}

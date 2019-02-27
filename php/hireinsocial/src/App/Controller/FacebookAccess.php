<?php

declare(strict_types=1);

namespace App\Controller;

use Facebook\Authentication\AccessToken;
use Facebook\Facebook;
use Psr\Log\LoggerInterface;

trait FacebookAccess
{
    public function getUserId(Facebook $facebook, AccessToken $accessToken, LoggerInterface $logger) : string
    {
        $facebookResponse = $facebook->get('me', $accessToken);

        $logger->debug('Facebook /me response', ['body' => $facebookResponse->getBody()]);

        return $facebookResponse->getDecodedBody()['id'];
    }
}

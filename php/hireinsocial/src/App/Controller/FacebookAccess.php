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

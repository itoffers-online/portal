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

namespace App\Offers\Controller;

use League\OAuth2\Client\Provider\LinkedIn;
use League\OAuth2\Client\Token\AccessTokenInterface;

trait LinkedInAccess
{
    /**
     * @return array{id: string, email: string}
     */
    public function getLinkedInUser(LinkedIn $linkedIn, AccessTokenInterface $accessToken) : array
    {
        $owner = $linkedIn->getResourceOwner($accessToken);

        return [
            'id' => $owner->getId(),
            'email' => $owner->getEmail(),
        ];
    }
}

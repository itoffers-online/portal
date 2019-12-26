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

namespace HireInSocial\Application\Facebook;

use HireInSocial\Application\Assertion;

final class Page
{
    private $fbId;

    private $accessToken;

    public function __construct(string $fbId, string $accessToken)
    {
        Assertion::betweenLength($fbId, 3, 255, 'Invalid FB Page ID');
        Assertion::betweenLength($accessToken, 3, 255, 'Invalid FB Page Token');

        $this->fbId = $fbId;
        $this->accessToken = $accessToken;
    }

    public function fbId() : string
    {
        return $this->fbId;
    }

    public function accessToken() : string
    {
        return $this->accessToken;
    }
}

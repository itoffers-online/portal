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
use HireInSocial\Application\Offer\Offer;
use Ramsey\Uuid\UuidInterface;

class Post
{
    private $fbId;

    private $jobOfferId;

    public function __construct(string $fbId, Offer $offer)
    {
        Assertion::betweenLength($fbId, 3, 255, 'Invalid FB Post ID');

        $this->fbId = $fbId;
        $this->jobOfferId = $offer->id();
    }

    public function fbId() : string
    {
        return $this->fbId;
    }

    public function jobOfferId() : UuidInterface
    {
        return $this->jobOfferId;
    }
}

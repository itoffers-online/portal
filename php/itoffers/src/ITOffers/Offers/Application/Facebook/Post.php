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

namespace ITOffers\Offers\Application\Facebook;

use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\Offer\Offer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Post
{
    /**
     * @var string
     */
    private $fbId;

    /**
     * @var string
     */
    private $jobOfferId;

    public function __construct(string $fbId, Offer $offer)
    {
        Assertion::betweenLength($fbId, 3, 255, 'Invalid FB Post ID');

        $this->fbId = $fbId;
        $this->jobOfferId = $offer->id()->toString();
    }

    public function fbId() : string
    {
        return $this->fbId;
    }

    public function jobOfferId() : UuidInterface
    {
        return Uuid::fromString($this->jobOfferId);
    }
}

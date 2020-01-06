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

namespace HireInSocial\Offers\Application\Facebook;

use HireInSocial\Offers\Application\Offer\Offer;
use HireInSocial\Offers\Application\Offer\OfferFormatter;
use HireInSocial\Offers\Application\User\User;
use Ramsey\Uuid\UuidInterface;

final class Draft
{
    /**
     * @var UuidInterface
     */
    private $userId;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $link;

    private function __construct(UuidInterface $userId, string $message, string $link)
    {
        $this->userId = $userId;
        $this->message = $message;
        $this->link = $link;
    }

    public static function createFor(User $user, OfferFormatter $formatter, Offer $offer, string $slug) : self
    {
        return new self($user->id(), $formatter->format($offer, $slug), $offer->company()->url());
    }

    public function __toString() : string
    {
        return $this->message;
    }

    public function link() : string
    {
        return $this->link;
    }

    public function userId() : UuidInterface
    {
        return $this->userId;
    }
}

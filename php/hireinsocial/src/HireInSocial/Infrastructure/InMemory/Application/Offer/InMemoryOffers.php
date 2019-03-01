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

namespace HireInSocial\Infrastructure\InMemory\Application\Offer;

use HireInSocial\Application\Offer\Offer;
use HireInSocial\Application\Offer\Offers;
use HireInSocial\Application\Offer\UserOffers;
use HireInSocial\Application\User\User;
use HireInSocial\Common\PrivateFields;

final class InMemoryOffers implements Offers
{
    use PrivateFields;

    private $offers;

    public function __construct(Offer ...$offers)
    {
        $this->offers = $offers;
    }

    public function add(Offer $offer): void
    {
        $this->offers[] = $offer;
    }

    public function postedBy(User $user, \DateTimeImmutable $since): UserOffers
    {
        return new UserOffers(
            $user,
            $since,
            ...array_filter(
                $this->offers,
                function (Offer $offer) use ($user) {
                    return self::getPrivatePropertyValue($offer, 'userId') === $user->id()->toString();
                }
            )
        );
    }
}

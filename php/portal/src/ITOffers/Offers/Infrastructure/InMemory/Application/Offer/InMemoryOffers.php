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

namespace ITOffers\Offers\Infrastructure\InMemory\Application\Offer;

use ITOffers\Component\Reflection\PrivateFields;
use ITOffers\Offers\Application\Offer\Offer;
use ITOffers\Offers\Application\Offer\Offers;
use ITOffers\Offers\Application\Offer\UserOffers;
use ITOffers\Offers\Application\User\User;
use Ramsey\Uuid\UuidInterface;

final class InMemoryOffers implements Offers
{
    use PrivateFields;

    /**
     * @var Offer[]
     */
    private array

 $offers;

    public function __construct(Offer ...$offers)
    {
        $this->offers = $offers;
    }

    public function add(Offer $offer) : void
    {
        $this->offers[] = $offer;
    }

    public function getById(UuidInterface $offerId) : Offer
    {
        return \current(\array_filter(
            $this->offers,
            fn (Offer $offer) => $offer->id()->equals($offerId)
        ));
    }

    public function postedBy(User $user, \DateTimeImmutable $since) : UserOffers
    {
        return new UserOffers(
            $user,
            $since,
            ...array_filter(
                $this->offers,
                fn (Offer $offer) => self::getPrivatePropertyValue($offer, 'userId') === $user->id()->toString()
            )
        );
    }
}

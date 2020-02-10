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

namespace HireInSocial\Notifications\Infrastructure\Offers;

use HireInSocial\Notifications\Application\Exception\Exception;
use HireInSocial\Notifications\Application\Offer\Offer;
use HireInSocial\Notifications\Application\Offers;
use HireInSocial\Offers\Offers as OffersModule;
use Ramsey\Uuid\UuidInterface;

final class ModuleOffers implements Offers
{
    /**
     * @var OffersModule
     */
    private $offersModule;

    public function __construct(OffersModule $offersModule)
    {
        $this->offersModule = $offersModule;
    }

    public function getById(UuidInterface $id) : Offer
    {
        $offer = $this->offersModule->offerQuery()->findById($id->toString());

        if (!$offer) {
            throw new Exception(\sprintf("Offer with id %s not found ", $id->toString()));
        }

        return new Offer(
            $offer->id(),
            $offer->contact()->email(),
            $offer->contact()->name(),
            $offer->slug(),
            $offer->specializationSlug(),
            $offer->position()->seniorityLevel(),
            $offer->position()->name(),
            $offer->company()->name(),
            $offer->company()->url()
        );
    }
}

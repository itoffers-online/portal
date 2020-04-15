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

namespace ITOffers\Offers\Application\Command\Offer;

use ITOffers\Component\CQRS\System\Command;
use ITOffers\Offers\Application\Command\ClassCommand;
use ITOffers\Offers\Application\Command\Offer\Offer\Offer;

final class UpdateOffer implements Command
{
    use ClassCommand;

    private string $offerId;

    private string $locale;

    private string $userId;

    private Offer $offer;

    private ?string $offerPDFPath = null;

    public function __construct(
        string $offerId,
        string $locale,
        string $userId,
        Offer $offer,
        ?string $offerPDFPath = null
    ) {
        $this->userId = $userId;
        $this->offer = $offer;
        $this->locale = $locale;
        $this->offerPDFPath = $offerPDFPath;
        $this->offerId = $offerId;
    }

    public function offerId() : string
    {
        return $this->offerId;
    }

    public function locale() : string
    {
        return $this->locale;
    }

    public function userId() : string
    {
        return $this->userId;
    }

    public function offer() : Offer
    {
        return $this->offer;
    }

    public function offerPDFPath() : ?string
    {
        return $this->offerPDFPath;
    }
}

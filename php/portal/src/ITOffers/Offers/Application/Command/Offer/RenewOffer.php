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

final class RenewOffer implements Command
{
    use ClassCommand;

    private string $offerId;

    public function __construct(string $offerId)
    {
        $this->offerId = $offerId;
    }

    public function offerId() : string
    {
        return $this->offerId;
    }
}

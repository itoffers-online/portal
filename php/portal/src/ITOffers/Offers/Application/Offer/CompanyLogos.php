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

namespace ITOffers\Offers\Application\Offer;

use Ramsey\Uuid\UuidInterface;

interface CompanyLogos
{
    public function add(CompanyLogo $companyLogo) : void;

    public function removeFor(UuidInterface $offerId) : void;
}

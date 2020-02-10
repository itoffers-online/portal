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

namespace ITOffers\Offers\Application\Query\Offer;

use ITOffers\Component\CQRS\System\Query;
use ITOffers\Offers\Application\Query\Offer\Model\Offer;
use ITOffers\Offers\Application\Query\Offer\Model\Offers;
use ITOffers\Offers\Application\Query\Offer\Model\OffersSeniorityLevel;

interface OfferQuery extends Query
{
    public function total() : int;

    public function count(OfferFilter $filter) : int;

    public function findAll(OfferFilter $filter) : Offers;

    public function offersSeniorityLevels(OfferFilter $filter) : OffersSeniorityLevel;

    public function findByEmailHash(string $emailHash) : ?Offer;

    public function findById(string $id) : ?Offer;

    public function findBySlug(string $slug) : ?Offer;

    public function findOneAfter(Offer $offer) : ?Offer;

    public function findOneBefore(Offer $offer) : ?Offer;
}

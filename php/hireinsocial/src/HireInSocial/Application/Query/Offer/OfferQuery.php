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

namespace HireInSocial\Application\Query\Offer;

use HireInSocial\Application\Query\Offer\Model\Offer;
use HireInSocial\Application\Query\Offer\Model\Offers;
use HireInSocial\Application\System\Query;

interface OfferQuery extends Query
{
    public function total() : int;
    public function count(OfferFilter $filter) : int;
    public function findAll(OfferFilter $filter) : Offers;
    public function findByEmailHash(string $emailHash) : ?Offer;
    public function findById(string $id) : ?Offer;
    public function findBySlug(string $slug) : ?Offer;
    public function findOneAfter(Offer $offer) : ?Offer;
    public function findOneBefore(Offer $offer) : ?Offer;
}

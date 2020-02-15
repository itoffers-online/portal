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

namespace ITOffers\Offers\Infrastructure\InMemory\Application\Specialization;

use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Offers\Application\Offer\Offer;
use ITOffers\Offers\Application\Specialization\Specialization;
use ITOffers\Offers\Application\Specialization\Specializations;

final class InMemorySpecializations implements Specializations
{
    /**
     * @var Specialization[]
     */
    private $specializations;

    public function __construct(Specialization ...$specializations)
    {
        $this->specializations = $specializations;
    }

    public function get(string $slug) : Specialization
    {
        foreach ($this->specializations as $specialization) {
            if ($specialization->is($slug)) {
                return $specialization;
            }
        }

        throw new Exception(sprintf('Specialization "%s" does not exists', $slug));
    }

    public function add(Specialization $specialization) : void
    {
        $this->specializations[] = $specialization;
    }

    public function getFor(Offer $offer) : Specialization
    {
        foreach ($this->specializations as $specialization) {
            if ($specialization->id()->equals($offer->specializationId())) {
                return $specialization;
            }
        }

        throw new Exception('Specialization does not exists');
    }
}

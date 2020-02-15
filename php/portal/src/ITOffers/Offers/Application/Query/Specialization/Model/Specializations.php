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

namespace ITOffers\Offers\Application\Query\Specialization\Model;

use ITOffers\Offers\Application\Query\Offer\Model\Offer;

final class Specializations extends \ArrayObject
{
    public function __construct(Specialization ...$specializations)
    {
        parent::__construct($specializations);
    }

    /**
     * @return Specialization[]
     */
    public function all() : array
    {
        return (array) $this;
    }

    public function getFor(Offer $offer) : Specialization
    {
        return \current(
            \array_filter(
                (array) $this,
                function (Specialization $specialization) use ($offer) {
                    return $specialization->is($offer->specializationSlug());
                }
            )
        );
    }

    public function has(string $slug) : bool
    {
        return (bool) \array_filter(
            (array) $this,
            function (Specialization $specialization) use ($slug) {
                return $specialization->is($slug);
            }
        );
    }

    public function get(string $slug) : Specialization
    {
        return \current(
            \array_filter(
                (array) $this,
                function (Specialization $specialization) use ($slug) {
                    return $specialization->is($slug);
                }
            )
        );
    }

    /**
     * @return Specialization[]
     */
    public function filterWithFacebook() : array
    {
        return \array_filter(
            (array) $this,
            function (Specialization $specialization) {
                return $specialization->facebookChannel();
            }
        );
    }

    /**
     * @return Specialization[]
     */
    public function filterWithTwitter() : array
    {
        return \array_filter(
            (array) $this,
            function (Specialization $specialization) {
                return $specialization->twitterChannel();
            }
        );
    }
}

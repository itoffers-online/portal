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

use ITOffers\Offers\Application\Query\Specialization\Model\Specialization\FacebookChannel;
use ITOffers\Offers\Application\Query\Specialization\Model\Specialization\Offers;
use ITOffers\Offers\Application\Query\Specialization\Model\Specialization\TwitterChannel;
use function mb_strtolower;

final class Specialization
{
    private string $slug;

    private Offers $offers;

    private ?FacebookChannel $facebookChannel = null;

    private ?TwitterChannel $twitterChannel = null;

    public function __construct(string $slug, Offers $offers, ?FacebookChannel $facebookChannel = null, ?TwitterChannel $twitterChannel = null)
    {
        $this->slug = $slug;
        $this->offers = $offers;
        $this->facebookChannel = $facebookChannel;
        $this->twitterChannel = $twitterChannel;
    }

    public function slug() : string
    {
        return $this->slug;
    }

    public function offers() : Offers
    {
        return $this->offers;
    }

    public function facebookChannel() : ?FacebookChannel
    {
        return $this->facebookChannel;
    }

    public function twitterChannel() : ?TwitterChannel
    {
        return $this->twitterChannel;
    }

    public function is(string $slug) : bool
    {
        return mb_strtolower($slug) === $this->slug;
    }
}

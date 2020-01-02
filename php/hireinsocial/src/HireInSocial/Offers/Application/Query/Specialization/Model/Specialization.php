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

namespace HireInSocial\Offers\Application\Query\Specialization\Model;

use HireInSocial\Offers\Application\Query\Specialization\Model\Specialization\FacebookChannel;
use HireInSocial\Offers\Application\Query\Specialization\Model\Specialization\Offers;
use function mb_strtolower;

final class Specialization
{
    /**
     * @var string
     */
    private $slug;

    /**
     * @var Offers
     */
    private $offers;

    /**
     * @var FacebookChannel|null
     */
    private $facebookChannel;

    public function __construct(string $slug, Offers $offers, ?FacebookChannel $facebookChannel)
    {
        $this->slug = $slug;
        $this->offers = $offers;
        $this->facebookChannel = $facebookChannel;
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

    public function is(string $slug) : bool
    {
        return mb_strtolower($slug) === $this->slug;
    }
}

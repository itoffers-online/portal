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

namespace ITOffers\Offers\Application\Query\Offer\Model;

final class OfferSeniorityLevel
{
    /**
     * @var int
     */
    private $level;

    /**
     * @var int
     */
    private $offersCount;

    public function __construct(int $level, int $offersCount)
    {
        $this->level = $level;
        $this->offersCount = $offersCount;
    }

    public function level() : int
    {
        return $this->level;
    }

    public function offersCount() : int
    {
        return $this->offersCount;
    }
}

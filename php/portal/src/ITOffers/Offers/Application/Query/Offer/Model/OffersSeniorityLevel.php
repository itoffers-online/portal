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

final class OffersSeniorityLevel extends \ArrayObject
{
    public function __construct(OfferSeniorityLevel ...$offerSeniorityLevels)
    {
        $levels = [];

        for ($level = 0; $level <= 4; $level++) {
            $levels[$level] = new OfferSeniorityLevel($level, 0);

            foreach ($offerSeniorityLevels as $seniorityLevel) {
                if ($seniorityLevel->level() === $level) {
                    $levels[$level] = $seniorityLevel;
                }
            }
        }

        parent::__construct($levels);
    }

    public function all() : array
    {
        return (array) $this;
    }
}

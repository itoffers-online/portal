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

namespace ITOffers\Offers\UserInterface;

use ITOffers\Offers\Application\Query\Specialization\Model\Specialization;

interface SpecializationThumbnail
{
    /**
     * Return path to generated thumbnail image
     */
    public function large(Specialization $specialization, bool $force = true) : string;
}

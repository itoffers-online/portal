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

namespace ITOffers\Offers\Application\Command\Specialization;

use ITOffers\Component\CQRS\System\Command;
use ITOffers\Offers\Application\Command\ClassCommand;

final class CreateSpecialization implements Command
{
    use ClassCommand;

    private string $slug;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    public function slug() : string
    {
        return $this->slug;
    }
}

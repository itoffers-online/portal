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

namespace HireInSocial\Offers\Infrastructure\InMemory\Application\Specialization;

use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\Specialization\Specialization;
use HireInSocial\Offers\Application\Specialization\Specializations;

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
}

<?php

declare (strict_types=1);

namespace HireInSocial\Infrastructure\InMemory\Application\Specialization;

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\Specialization\Specialization;
use HireInSocial\Application\Specialization\Specializations;

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

    public function get(string $slug): Specialization
    {
        foreach ($this->specializations as $specialization) {
            if ($specialization->is($slug)) {
                return $specialization;
            }
        }

        throw new Exception(sprintf('Specialization "%s" does not exists', $slug));
    }
}
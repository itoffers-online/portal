<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\Specialization;

use HireInSocial\Application\Command\ClassCommand;
use HireInSocial\Application\System\Command;

class RemoveFacebookChannel implements Command
{
    use ClassCommand;

    private $specSlug;

    public function __construct(string $specSlug)
    {
        $this->specSlug = $specSlug;
    }

    public function specSlug(): string
    {
        return $this->specSlug;
    }
}

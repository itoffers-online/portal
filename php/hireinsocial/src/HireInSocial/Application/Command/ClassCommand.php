<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command;

trait ClassCommand
{
    public function commandName() : string
    {
        return self::class;
    }
}

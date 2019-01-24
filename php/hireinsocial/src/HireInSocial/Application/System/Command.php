<?php

declare(strict_types=1);

namespace HireInSocial\Application\System;

interface Command
{
    public function name() : string;
}

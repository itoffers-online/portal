<?php

declare (strict_types=1);

namespace HireInSocial\Application\System;

interface Calendar
{
    public function currentTime() : \DateTimeImmutable;
}
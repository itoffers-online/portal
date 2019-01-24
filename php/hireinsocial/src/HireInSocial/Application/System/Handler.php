<?php

declare (strict_types=1);

namespace HireInSocial\Application\System;

interface Handler
{
    public function handles() : string;
}
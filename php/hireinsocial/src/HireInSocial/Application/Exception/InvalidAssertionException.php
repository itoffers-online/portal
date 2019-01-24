<?php

declare (strict_types=1);

namespace HireInSocial\Application\Exception;

class InvalidAssertionException extends Exception
{
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }
}
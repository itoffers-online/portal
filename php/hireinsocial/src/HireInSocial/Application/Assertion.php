<?php

declare (strict_types=1);

namespace HireInSocial\Application;

use Assert\Assertion as BaseAssertion;
use HireInSocial\Application\Exception\InvalidAssertionException;

final class Assertion extends BaseAssertion
{
    protected static $exceptionClass = InvalidAssertionException::class;
}
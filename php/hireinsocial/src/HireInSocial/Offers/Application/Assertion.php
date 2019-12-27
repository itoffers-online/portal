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

namespace HireInSocial\Offers\Application;

use Assert\Assertion as BaseAssertion;
use HireInSocial\Offers\Application\Exception\InvalidAssertionException;

final class Assertion extends BaseAssertion
{
    /**
     * @var string
     */
    protected static $exceptionClass = InvalidAssertionException::class;
}

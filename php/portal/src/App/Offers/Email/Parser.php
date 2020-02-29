<?php

declare(strict_types=1);

/*
 * This file is part of the itoffers.online project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Offers\Email;

use Assert\Assertion;

final class Parser
{
    private string $raw;

    public function __construct(string $raw)
    {
        Assertion::email($raw);
        $this->raw = $raw;
    }

    public function parse() : Email
    {
        $regexp = '/^([-0-9a-zA-Z._]+)(\+*[-0-9a-zA-Z._]*)@([-0-9a-zA-Z.+_]+)$/';

        \preg_match($regexp, $this->raw, $matches);

        return new Email(
            $matches[1],
            $matches[2],
            $matches[3]
        );
    }
}

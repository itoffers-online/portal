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

namespace HireInSocial\Offers\Application\Offer\Application;

use HireInSocial\Offers\Application\Assertion;
use HireInSocial\Offers\Application\Hash\Encoder;

final class EmailHash
{
    /**
     * @var string
     */
    private $hash;

    private function __construct(string $hash)
    {
        $this->hash = $hash;
    }

    public static function fromRaw(string $email, Encoder $encoder) : self
    {
        Assertion::email($email);
        Assertion::notContains($email, '+', 'Email address can\'t contain + character.');
        Assertion::true(\function_exists('imap_rfc822_parse_adrlist'), 'PHP imap extension is missing.');

        $parts = \current(\imap_rfc822_parse_adrlist($email, ''));
        $hash = new self(
            $encoder->encode(\str_replace('.', '', $parts->mailbox). '@' . $parts->host)
        );

        return $hash;
    }

    public static function fromHash(string $hash) : self
    {
        return new self($hash);
    }

    public function toString() : string
    {
        return $this->hash;
    }
}

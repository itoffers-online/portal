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

final class Email
{
    private string $local;

    private string $tag;

    private string $domain;

    public function __construct(string $local, string $tag, string $domain)
    {
        $this->local = $local;
        $this->tag = \ltrim($tag, '+');
        $this->domain = $domain;
    }

    public function local() : string
    {
        return $this->local;
    }

    public function hasTag() : bool
    {
        return \mb_strlen($this->tag) > 0;
    }

    public function tag() : string
    {
        return $this->tag;
    }

    public function domain() : string
    {
        return $this->domain;
    }

    public function toString() : string
    {
        return $this->local . (\mb_strlen($this->tag) ? '+' . $this->tag : '') . '@' . $this->domain;
    }
}

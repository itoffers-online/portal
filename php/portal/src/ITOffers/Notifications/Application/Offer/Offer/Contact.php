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

namespace ITOffers\Notifications\Application\Offer\Offer;

final class Contact
{
    private ?string $email = null;

    private ?string $name = null;

    private ?string $url = null;

    private function __construct()
    {
    }

    public static function recruiter(string $email, string $name) : self
    {
        $contact = new self();
        $contact->email = $email;
        $contact->name = $name;

        return $contact;
    }

    public static function externalSource(string $url) : self
    {
        $contact = new self();
        $contact->url = $url;

        return $contact;
    }

    public function email() : string
    {
        return $this->email;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function isRecruiter() : bool
    {
        return $this->email !== null;
    }

    public function isExternalSource() : bool
    {
        return $this->url !== null;
    }
}

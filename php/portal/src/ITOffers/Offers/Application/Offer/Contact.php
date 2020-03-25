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

namespace ITOffers\Offers\Application\Offer;

use ITOffers\Offers\Application\Assertion;

final class Contact
{
    private ?string $email = null;

    private ?string $name = null;

    private ?string $phone = null;

    private ?string $url = null;

    private function __construct()
    {
    }

    public static function recruiter(string $email, string $name, ?string $phone = null) : self
    {
        Assertion::email($email);
        Assertion::betweenLength($name, 3, 255);

        if ($phone) {
            Assertion::betweenLength($phone, 6, 16);
        }

        $contact = new self();

        $contact->email = $email;
        $contact->name = $name;
        $contact->phone = $phone;

        return $contact;
    }

    public static function externalSource(string $url) : self
    {
        Assertion::url($url);
        Assertion::betweenLength($url, 3, 2_083);

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

    public function phone() : ?string
    {
        return $this->phone;
    }
}

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

namespace ITOffers\Offers\Application\Query\Offer\Model\Offer;

final class Contact
{
    private ?string $email = null;

    private ?string $name = null;

    private ?string $phone = null;

    private ?string $url = null;

    public function __construct()
    {
    }

    public static function recruiter(string $email, string $name, ?string $phone = null) : self
    {
        $contact = new self();

        $contact->email = $email;
        $contact->name = $name;
        $contact->phone = $phone;

        return $contact;
    }

    public static function externalSource(string $url) : self
    {
        $contact = new self();
        $contact->url = $url;

        return $contact;
    }

    public function url() : ?string
    {
        return $this->url;
    }

    public function email() : ?string
    {
        return $this->email;
    }

    public function name() : ?string
    {
        return $this->name;
    }

    public function phone() : ?string
    {
        return $this->phone;
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

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

namespace ITOffers\Offers\Application\Query\Offer\Model;

use Aeon\Calendar\Gregorian\DateTime;
use ITOffers\Offers\Application\Query\Offer\Model\Offer\Company;
use ITOffers\Offers\Application\Query\Offer\Model\Offer\Contact;
use ITOffers\Offers\Application\Query\Offer\Model\Offer\Contract;
use ITOffers\Offers\Application\Query\Offer\Model\Offer\Description;
use ITOffers\Offers\Application\Query\Offer\Model\Offer\Location;
use ITOffers\Offers\Application\Query\Offer\Model\Offer\OfferPDF;
use ITOffers\Offers\Application\Query\Offer\Model\Offer\Parameters;
use ITOffers\Offers\Application\Query\Offer\Model\Offer\Position;
use ITOffers\Offers\Application\Query\Offer\Model\Offer\Salary;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Offer
{
    private UuidInterface $id;

    private string $slug;

    private string $emailHash;

    private string $locale;

    private ?UuidInterface $userId = null;

    private string $specializationSlug;

    private DateTime $createdAt;

    private Parameters $parameters;

    private int $applicationsCount;

    private ?OfferPDF $offerPDF = null;

    public function __construct(
        UuidInterface $id,
        string $slug,
        string $emailHash,
        string $locale,
        UuidInterface $userId,
        string $specializationSlug,
        DateTime $createdAt,
        Parameters $parameters,
        int $applicationsCount,
        ?OfferPDF $offerPDF
    ) {
        $this->slug = $slug;
        $this->id = $id;
        $this->emailHash = $emailHash;
        $this->userId = $userId;
        $this->createdAt = $createdAt;
        $this->specializationSlug = $specializationSlug;
        $this->parameters = $parameters;
        $this->applicationsCount = $applicationsCount;
        $this->offerPDF = $offerPDF;
        $this->locale = $locale;
    }

    public function id() : UuidInterface
    {
        return $this->id;
    }

    public function userId() : ?UuidInterface
    {
        return $this->userId;
    }

    public function locale() : string
    {
        return $this->locale;
    }

    public function emailHash() : string
    {
        return $this->emailHash;
    }

    public function slug() : string
    {
        return $this->slug;
    }

    public function specializationSlug() : string
    {
        return $this->specializationSlug;
    }

    public function createdAt() : DateTime
    {
        return $this->createdAt;
    }

    public function company() : Company
    {
        return $this->parameters->company();
    }

    public function contact() : Contact
    {
        return $this->parameters->contact();
    }

    public function contract() : Contract
    {
        return $this->parameters->contract();
    }

    public function description() : Description
    {
        return $this->parameters->description();
    }

    public function location() : Location
    {
        return $this->parameters->location();
    }

    public function position() : Position
    {
        return $this->parameters->position();
    }

    public function salary() : ?Salary
    {
        return $this->parameters->salary();
    }

    public function offerPDF() : ?OfferPDF
    {
        return $this->offerPDF;
    }

    public function postedBy(string $userId) : bool
    {
        return $this->userId->equals(Uuid::fromString($userId));
    }

    public function applicationsCount() : int
    {
        return $this->applicationsCount;
    }
}

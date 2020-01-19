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

namespace HireInSocial\Offers\Application\Query\Offer\Model;

use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Company;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Contact;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Contract;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Description;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Location;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\OfferPDF;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Parameters;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Position;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Salary;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Offer
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $emailHash;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var UuidInterface|null
     */
    private $userId;

    /**
     * @var string
     */
    private $specializationSlug;

    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var Parameters
     */
    private $parameters;

    /**
     * @var int
     */
    private $applicationsCount;

    /**
     * @var OfferPDF|null
     */
    private $offerPDF;

    public function __construct(
        UuidInterface $id,
        string $slug,
        string $emailHash,
        string $locale,
        UuidInterface $userId,
        string $specializationSlug,
        \DateTimeImmutable $createdAt,
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

    /**
     * @return string
     */
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

    public function createdAt() : \DateTimeImmutable
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

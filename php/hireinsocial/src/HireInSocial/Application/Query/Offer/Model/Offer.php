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

namespace HireInSocial\Application\Query\Offer\Model;

use HireInSocial\Application\Query\Offer\Model\Offer\Company;
use HireInSocial\Application\Query\Offer\Model\Offer\Contact;
use HireInSocial\Application\Query\Offer\Model\Offer\Contract;
use HireInSocial\Application\Query\Offer\Model\Offer\Description;
use HireInSocial\Application\Query\Offer\Model\Offer\Location;
use HireInSocial\Application\Query\Offer\Model\Offer\OfferPDF;
use HireInSocial\Application\Query\Offer\Model\Offer\Parameters;
use HireInSocial\Application\Query\Offer\Model\Offer\Position;
use HireInSocial\Application\Query\Offer\Model\Offer\Salary;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Offer
{
    private $id;
    private $slug;
    private $emailHash;
    private $userId;
    private $specializationSlug;
    private $createdAt;
    private $parameters;
    private $offerPDF;

    public function __construct(
        UuidInterface $id,
        string $slug,
        string $emailHash,
        UuidInterface $userId,
        string $specializationSlug,
        \DateTimeImmutable $createdAt,
        Parameters $parameters,
        ?OfferPDF $offerPDF
    ) {
        $this->slug = $slug;
        $this->id = $id;
        $this->emailHash = $emailHash;
        $this->userId = $userId;
        $this->createdAt = $createdAt;
        $this->specializationSlug = $specializationSlug;
        $this->parameters = $parameters;
        $this->offerPDF = $offerPDF;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function userId(): ?UuidInterface
    {
        return $this->userId;
    }

    public function emailHash(): string
    {
        return $this->emailHash;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function specializationSlug(): string
    {
        return $this->specializationSlug;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function company(): Company
    {
        return $this->parameters->company();
    }

    public function contact(): Contact
    {
        return $this->parameters->contact();
    }

    public function contract(): Contract
    {
        return $this->parameters->contract();
    }

    public function description(): Description
    {
        return $this->parameters->description();
    }

    public function location(): Location
    {
        return $this->parameters->location();
    }

    public function position(): Position
    {
        return $this->parameters->position();
    }

    public function salary(): ?Salary
    {
        return $this->parameters->salary();
    }

    public function offerPDF(): ?OfferPDF
    {
        return $this->offerPDF;
    }

    public function postedBy(string $userId) : bool
    {
        return $this->userId->equals(Uuid::fromString($userId));
    }
}

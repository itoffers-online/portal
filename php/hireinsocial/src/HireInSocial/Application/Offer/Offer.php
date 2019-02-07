<?php

declare(strict_types=1);

namespace HireInSocial\Application\Offer;

use HireInSocial\Application\System\Calendar;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Offer
{
    private $id;
    private $specializationId;
    private $createdAt;
    private $company;
    private $position;
    private $location;
    private $salary;
    private $contract;
    private $description;
    private $contact;

    public function __construct(
        UuidInterface $specializationId,
        Company $company,
        Position $position,
        Location $location,
        ?Salary $salary,
        Contract $contract,
        Description $description,
        Contact $contact,
        Calendar $calendar
    ) {
        $this->id = (string) Uuid::uuid4();
        $this->specializationId = $specializationId;
        $this->createdAt = $calendar->currentTime();
        $this->company = $company;
        $this->position = $position;
        $this->location = $location;
        $this->salary = $salary;
        $this->contract = $contract;
        $this->description = $description;
        $this->contact = $contact;
    }

    public function id(): UuidInterface
    {
        return Uuid::fromString($this->id);
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function company(): Company
    {
        return $this->company;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function location(): Location
    {
        return $this->location;
    }

    public function salary(): ?Salary
    {
        return $this->salary;
    }

    public function contract(): Contract
    {
        return $this->contract;
    }

    public function description(): Description
    {
        return $this->description;
    }

    public function contact(): Contact
    {
        return $this->contact;
    }
}

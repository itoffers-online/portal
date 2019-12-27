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

namespace HireInSocial\Offers\Application\Offer;

use DateTimeImmutable;
use Hashids\Hashids;
use HireInSocial\Offers\Application\Assertion;
use HireInSocial\Offers\Application\Specialization\Specialization;
use HireInSocial\Offers\Application\System\Calendar;
use HireInSocial\Offers\Application\User\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Offer
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $emailHash;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var UuidInterface
     */
    private $specializationId;

    /**
     * @var DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var Company
     */
    private $company;

    /**
     * @var Position
     */
    private $position;

    /**
     * @var Location
     */
    private $location;

    /**
     * @var Salary|null
     */
    private $salary;

    /**
     * @var Contract
     */
    private $contract;

    /**
     * @var Description
     */
    private $description;

    /**
     * @var Contact
     */
    private $contact;

    /**
     * @var DateTimeImmutable
     */
    private $removedAt;

    private function __construct(
        UuidInterface $userId,
        UuidInterface $specializationId,
        Company $company,
        Position $position,
        Location $location,
        ?Salary $salary,
        Contract $contract,
        Description $description,
        Contact $contact,
        DateTimeImmutable $createdAt
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->userId = $userId->toString();
        $this->emailHash = (new Hashids())->encode(time() + \random_int(0, 5000));
        $this->specializationId = $specializationId;
        $this->company = $company;
        $this->position = $position;
        $this->location = $location;
        $this->salary = $salary;
        $this->contract = $contract;
        $this->description = $description;
        $this->contact = $contact;
        $this->createdAt = $createdAt;
    }

    public static function postIn(
        Specialization $specialization,
        User $user,
        Company $company,
        Position $position,
        Location $location,
        ?Salary $salary,
        Contract $contract,
        Description $description,
        Contact $contact,
        Calendar $calendar
    ) : self {
        return new self(
            $user->id(),
            $specialization->id(),
            $company,
            $position,
            $location,
            $salary,
            $contract,
            $description,
            $contact,
            $calendar->currentTime()
        );
    }

    public function id() : UuidInterface
    {
        return Uuid::fromString($this->id);
    }

    public function createdAt() : DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function company() : Company
    {
        return $this->company;
    }

    public function position() : Position
    {
        return $this->position;
    }

    public function location() : Location
    {
        return $this->location;
    }

    public function salary() : ?Salary
    {
        return $this->salary;
    }

    public function contract() : Contract
    {
        return $this->contract;
    }

    public function description() : Description
    {
        return $this->description;
    }

    public function contact() : Contact
    {
        return $this->contact;
    }

    public function remove(User $user, Calendar $calendar) : void
    {
        Assertion::true(Uuid::fromString($this->userId)->equals($user->id()));

        $this->removedAt = $calendar->currentTime();
    }
}

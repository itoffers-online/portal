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

use DateTimeImmutable;
use Hashids\Hashids;
use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\Calendar;
use ITOffers\Offers\Application\Specialization\Specialization;
use ITOffers\Offers\Application\User\User;
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
     * @var string
     */
    private $specializationId;

    /**
     * @var Locale
     */
    private $locale;

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
        UuidInterface $id,
        UuidInterface $userId,
        UuidInterface $specializationId,
        Locale $locale,
        Company $company,
        Position $position,
        Location $location,
        ?Salary $salary,
        Contract $contract,
        Description $description,
        Contact $contact,
        DateTimeImmutable $createdAt
    ) {
        $this->id = $id->toString();
        $this->userId = $userId->toString();
        $this->emailHash = (new Hashids())->encode(time() + \random_int(0, 5000));
        $this->specializationId = $specializationId->toString();
        $this->locale = $locale;
        $this->company = $company;
        $this->position = $position;
        $this->location = $location;
        $this->salary = $salary;
        $this->contract = $contract;
        $this->description = $description;
        $this->contact = $contact;
        $this->createdAt = $createdAt;
    }

    public static function post(
        UuidInterface $id,
        Specialization $specialization,
        Locale $locale,
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
            $id,
            $user->id(),
            $specialization->id(),
            $locale,
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

    public function getUserId() : UuidInterface
    {
        return Uuid::fromString($this->userId);
    }

    public function specializationId() : UuidInterface
    {
        return Uuid::fromString($this->specializationId);
    }

    public function locale() : Locale
    {
        return $this->locale;
    }

    public function emailHash() : string
    {
        return $this->emailHash;
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

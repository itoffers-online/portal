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

use Aeon\Calendar\Gregorian\Calendar;
use Aeon\Calendar\Gregorian\DateTime;
use Hashids\Hashids;
use ITOffers\Offers\Application\Assertion;
use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Offers\Application\Specialization\Specialization;
use ITOffers\Offers\Application\User\OfferAutoRenews;
use ITOffers\Offers\Application\User\User;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Offer
{
    public const INSTANT_EDIT_TIME_HOURS = 4;

    private string $id;

    private string $emailHash;

    private string $userId;

    private string $specializationId;

    private Locale $locale;

    private DateTime $createdAt;

    private Company $company;

    private Position $position;

    private Location $location;

    private ?Salary $salary = null;

    private Contract $contract;

    private Description $description;

    private Contact $contact;

    private ?DateTime $removedAt = null;

    private ?DateTime $updatedAt = null;

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
        DateTime $createdAt
    ) {
        $this->id = $id->toString();
        $this->userId = $userId->toString();
        $this->emailHash = (new Hashids())->encode(time() + \random_int(0, 5_000));
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
            $calendar->now()
        );
    }

    public function id() : UuidInterface
    {
        return Uuid::fromString($this->id);
    }

    public function userId() : UuidInterface
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

    public function createdAt() : DateTime
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

    public function update(
        User $user,
        Locale $locale,
        Company $company,
        Position $position,
        Location $location,
        ?Salary $salary,
        Contract $contract,
        Description $description,
        Contact $contact,
        Calendar $calendar
    ) : void {
        if (!$user->id()->equals($this->userId())) {
            throw new Exception("User is not allowed to update the offer");
        }

        if ($this->createdAt->modify(\sprintf('+%d hours', self::INSTANT_EDIT_TIME_HOURS))->isBeforeOrEqual($calendar->now())) {
            throw new Exception("This offer can't be updated anymore");
        }

        $this->locale = $locale;
        $this->company = $company;
        $this->position = $position;
        $this->location = $location;
        $this->salary = $salary;
        $this->contract = $contract;
        $this->description = $description;
        $this->contact = $contact;
        $this->updatedAt = $calendar->now();
    }

    public function remove(User $user, Calendar $calendar) : void
    {
        Assertion::true(Uuid::fromString($this->userId)->equals($user->id()));

        $this->removedAt = $calendar->now();
    }

    public function renew(OfferAutoRenews $offerAutoRenews, Calendar $calendar) : void
    {
        $offerAutoRenews->getUnusedFor($this->id())
            ->renew($this, $calendar);
        $this->createdAt = $calendar->now();
    }
}

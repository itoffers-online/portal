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

namespace HireInSocial\Application\Command\Offer\Offer;

final class Offer
{
    /**
     * @var \HireInSocial\Application\Command\Offer\Offer\Company
     */
    private $company;

    /**
     * @var \HireInSocial\Application\Command\Offer\Offer\Position
     */
    private $position;

    /**
     * @var \HireInSocial\Application\Command\Offer\Offer\Location
     */
    private $location;

    /**
     * @var \HireInSocial\Application\Command\Offer\Offer\Salary|null
     */
    private $salary;

    /**
     * @var \HireInSocial\Application\Command\Offer\Offer\Contract
     */
    private $contract;

    /**
     * @var \HireInSocial\Application\Command\Offer\Offer\Description
     */
    private $description;

    /**
     * @var \HireInSocial\Application\Command\Offer\Offer\Contact
     */
    private $contact;

    /**
     * @var \HireInSocial\Application\Command\Offer\Offer\Channels
     */
    private $channels;

    public function __construct(
        Company $company,
        Position $position,
        Location $location,
        ?Salary $salary,
        Contract $contract,
        Description $description,
        Contact $contact,
        Channels $channels
    ) {
        $this->company = $company;
        $this->position = $position;
        $this->location = $location;
        $this->salary = $salary;
        $this->contract = $contract;
        $this->description = $description;
        $this->contact = $contact;
        $this->channels = $channels;
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

    public function channels() : Channels
    {
        return $this->channels;
    }
}

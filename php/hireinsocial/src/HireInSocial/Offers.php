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

namespace HireInSocial;

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\Query\Offer\ApplicationQuery;
use HireInSocial\Application\Query\Offer\OfferQuery;
use HireInSocial\Application\Query\Offer\OfferThrottleQuery;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\Query\User\UserQuery;
use HireInSocial\Application\System;

final class Offers
{
    /**
     * @var System
     */
    private $system;

    /**
     * @var System\Calendar
     */
    private $calendar;

    public function __construct(System $system, System\Calendar $calendar)
    {
        $this->system = $system;
        $this->calendar = $calendar;
    }

    public function calendar() : System\Calendar
    {
        return $this->calendar;
    }

    public function handle(System\Command $command) : void
    {
        $this->system->handle($command);
    }

    public function offerQuery() : OfferQuery
    {
        $query = $this->system->query(OfferQuery::class);

        if (!$query instanceof OfferQuery) {
            throw new Exception("Expected OfferQuery but got " . \get_class($query));
        }

        return $query;
    }

    public function offerThrottleQuery() : OfferThrottleQuery
    {
        $query = $this->system->query(OfferThrottleQuery::class);

        if (!$query instanceof OfferThrottleQuery) {
            throw new Exception("Expected OfferThrottleQuery but got " . \get_class($query));
        }

        return $query;
    }

    public function applicationQuery() : ApplicationQuery
    {
        $query = $this->system->query(ApplicationQuery::class);

        if (!$query instanceof ApplicationQuery) {
            throw new Exception("Expected ApplicationQuery but got " . \get_class($query));
        }

        return $query;
    }

    public function specializationQuery() : SpecializationQuery
    {
        $query = $this->system->query(SpecializationQuery::class);

        if (!$query instanceof SpecializationQuery) {
            throw new Exception("Expected SpecializationQuery but got " . \get_class($query));
        }

        return $query;
    }

    public function userQuery() : UserQuery
    {
        $query = $this->system->query(UserQuery::class);

        if (!$query instanceof UserQuery) {
            throw new Exception("Expected UserQuery but got " . \get_class($query));
        }

        return $query;
    }
}

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

namespace HireInSocial\Offers;

use HireInSocial\Component\CQRS\System;
use HireInSocial\Component\CQRS\System\Command;
use HireInSocial\Offers\Application\Calendar;
use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\Query\Facebook\FacebookQuery;
use HireInSocial\Offers\Application\Query\Features\FeatureToggleQuery;
use HireInSocial\Offers\Application\Query\Offer\ApplicationQuery;
use HireInSocial\Offers\Application\Query\Offer\OfferQuery;
use HireInSocial\Offers\Application\Query\Offer\OfferThrottleQuery;
use HireInSocial\Offers\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Offers\Application\Query\Twitter\TweetsQuery;
use HireInSocial\Offers\Application\Query\User\ExtraOffersQuery;
use HireInSocial\Offers\Application\Query\User\UserQuery;

/**
 * Module - Offers
 *
 * This module is responsible for posting and managing job offers, specializations and sales channels.
 * It also provides very basic users base (that might go to separated module in the future).
 */
final class Offers
{
    /**
     * @var System
     */
    private $system;

    /**
     * @var Calendar
     */
    private $calendar;

    public function __construct(System $system, Calendar $calendar)
    {
        $this->system = $system;
        $this->calendar = $calendar;
    }

    public function calendar() : Calendar
    {
        return $this->calendar;
    }

    public function handle(Command $command) : void
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

    public function facebookPostQuery() : FacebookQuery
    {
        $query = $this->system->query(FacebookQuery::class);

        if (!$query instanceof FacebookQuery) {
            throw new Exception("Expected FacebookQuery but got " . \get_class($query));
        }

        return $query;
    }

    public function tweetsQuery() : TweetsQuery
    {
        $query = $this->system->query(TweetsQuery::class);

        if (!$query instanceof TweetsQuery) {
            throw new Exception("Expected TweetsQuery but got " . \get_class($query));
        }

        return $query;
    }

    public function extraOffersQuery() : ExtraOffersQuery
    {
        $query = $this->system->query(ExtraOffersQuery::class);

        if (!$query instanceof ExtraOffersQuery) {
            throw new Exception("Expected ExtraOffersQuery but got " . \get_class($query));
        }

        return $query;
    }

    public function featureQuery() : FeatureToggleQuery
    {
        $query = $this->system->query(FeatureToggleQuery::class);

        if (!$query instanceof FeatureToggleQuery) {
            throw new Exception("Expected FeatureToggleQuery but got " . \get_class($query));
        }

        return $query;
    }
}

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

namespace HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Application\Offer;

use function array_key_exists;
use DateInterval;
use DateTimeInterface;
use Doctrine\DBAL\Connection;
use HireInSocial\Offers\Application\Query\Offer\OfferThrottleQuery;
use HireInSocial\Offers\Application\System\Calendar;

final class DbalOfferThrottleQuery implements OfferThrottleQuery
{
    /**
     * @var int[]
     */
    private $cache;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var DateInterval
     */
    private $since;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Calendar
     */
    private $calendar;

    public function __construct(int $limit, DateInterval $since, Connection $connection, Calendar $calendar)
    {
        $this->limit = $limit;
        $this->since = $since;
        $this->connection = $connection;
        $this->calendar = $calendar;
        $this->cache = [];
    }

    public function limit() : int
    {
        return $this->limit;
    }

    public function since() : DateInterval
    {
        return $this->since;
    }

    public function isThrottled(string $userId) : bool
    {
        if (array_key_exists($userId, $this->cache)) {
            return $this->cache[$userId] >= $this->limit;
        }

        $postedOffers = $this->postedOffers($userId);

        $this->cache[$userId] = $postedOffers;

        return $postedOffers >= $this->limit;
    }

    public function offersLeft(string $userId) : int
    {
        if (array_key_exists($userId, $this->cache)) {
            $offersLeft = $this->limit - $this->cache[$userId];

            return $offersLeft < 0 ? 0 : $offersLeft;
        }

        $postedOffers = $this->postedOffers($userId);

        $this->cache[$userId] = $postedOffers;

        $offersLeft = $this->limit - $postedOffers;

        return $offersLeft < 0 ? 0 : $offersLeft;
    }

    private function postedOffers(string $userId) : int
    {
        return (int) $this->connection->createQueryBuilder()
            ->select('COUNT(o.*)')
            ->from('his_job_offer', 'o')
            ->where('o.user_id = :userId')
            ->andWhere('o.created_at >= :since')
            ->setParameters([
                'userId' => $userId,
                'since' => $this->calendar->currentTime()->sub($this->since)->format(DateTimeInterface::ISO8601),
            ])
            ->execute()
            ->fetchColumn();
    }
}

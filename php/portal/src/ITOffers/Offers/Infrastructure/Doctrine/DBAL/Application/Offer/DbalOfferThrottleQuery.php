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

namespace ITOffers\Offers\Infrastructure\Doctrine\DBAL\Application\Offer;

use Aeon\Calendar\TimeUnit;
use function array_key_exists;
use Doctrine\DBAL\Connection;
use Aeon\Calendar\Gregorian\Calendar;
use ITOffers\Offers\Application\Query\Offer\OfferThrottleQuery;

final class DbalOfferThrottleQuery implements OfferThrottleQuery
{
    /**
     * @var int[]
     */
    private array $cache;

    private int $limit;

    private TimeUnit $since;

    private Connection $connection;

    private Calendar $calendar;

    public function __construct(int $limit, TimeUnit $since, Connection $connection, Calendar $calendar)
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

    public function since() : TimeUnit
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
            ->from('itof_job_offer', 'o')
            ->where('o.user_id = :userId')
            ->andWhere('o.created_at >= :since')
            ->setParameters([
                'userId' => $userId,
                'since' => $this->calendar
                    ->now()
                    ->sub($this->since)
                    ->format($this->connection->getDatabasePlatform()->getDateTimeFormatString()),
            ])
            ->execute()
            ->fetchColumn();
    }
}

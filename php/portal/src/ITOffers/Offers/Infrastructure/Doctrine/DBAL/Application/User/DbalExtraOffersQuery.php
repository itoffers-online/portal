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

namespace ITOffers\Offers\Infrastructure\Doctrine\DBAL\Application\User;

use Doctrine\DBAL\Connection;
use ITOffers\Offers\Application\Query\User\ExtraOffersQuery;
use ITOffers\Offers\Application\Query\User\Model\ExtraOffer;

final class DbalExtraOffersQuery implements ExtraOffersQuery
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function countNotExpired(string $userId) : int
    {
        return (int) $this->connection->createQueryBuilder()
            ->select('COUNT(eo.*)')
            ->from('itof_extra_offer', 'eo')
            ->where('eo.user_id = :userId')
            ->andWhere('eo.expires_at > NOW()')
            ->andWhere('eo.used_at IS NULL')
            ->setParameters(
                [
                    'userId' => $userId,
                ]
            )->execute()
            ->fetchColumn();
    }

    public function findClosesToExpire(string $userId) : ?ExtraOffer
    {
        $extraOffersData = $this->connection->createQueryBuilder()
            ->select('eo.*')
            ->from('itof_extra_offer', 'eo')
            ->where('eo.user_id = :userId')
            ->andWhere('eo.expires_at > NOW()')
            ->andWhere('eo.used_at IS NULL')
            ->setParameters(
                [
                    'userId' => $userId,
                ]
            )->orderBy('eo.expires_at', 'ASC')
            ->setMaxResults(1)
            ->execute()
            ->fetchAll();

        if (!$extraOffersData) {
            return null;
        }

        $firstExtraOffer = \current($extraOffersData);

        return new ExtraOffer(
            $firstExtraOffer['user_id'],
            \Aeon\Calendar\Gregorian\DateTime::fromString($firstExtraOffer['expires_at'])
        );
    }
}

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
use ITOffers\Offers\Application\Query\User\Model\OfferAutoRenew;
use ITOffers\Offers\Application\Query\User\OfferAutoRenewQuery;

final class DbalOfferAutoRenewQuery implements OfferAutoRenewQuery
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function countUnassignedNotExpired(string $userId) : int
    {
        return (int) $this->connection->createQueryBuilder()
            ->select('COUNT(oar.*)')
            ->from('itof_offer_auto_renew', 'oar')
            ->where('oar.user_id = :userId')
            ->andWhere('oar.expires_at > NOW()')
            ->andWhere('oar.renewed_at IS NULL')
            ->andWhere('oar.offer_id IS NULL')
            ->setParameters(
                [
                    'userId' => $userId,
                ]
            )->execute()
            ->fetchColumn();
    }

    public function findUnassignedClosesToExpire(string $userId) : ?OfferAutoRenew
    {
        $extraOffersData = $this->connection->createQueryBuilder()
            ->select('oar.*')
            ->from('itof_offer_auto_renew', 'oar')
            ->where('oar.user_id = :userId')
            ->andWhere('oar.expires_at > NOW()')
            ->andWhere('oar.renewed_at IS NULL')
            ->andWhere('oar.offer_id IS NULL')
            ->setParameters(
                [
                    'userId' => $userId,
                ]
            )->orderBy('oar.expires_at', 'ASC')
            ->setMaxResults(1)
            ->execute()
            ->fetchAll();

        if (!$extraOffersData) {
            return null;
        }

        $firstExtraOffer = \current($extraOffersData);

        return new OfferAutoRenew(
            $firstExtraOffer['user_id'],
            new \DateTimeImmutable($firstExtraOffer['expires_at'])
        );
    }
}

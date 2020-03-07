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
use ITOffers\Offers\Application\Query\User\Model\UnassignedAutoRenew;
use ITOffers\Offers\Application\Query\User\OfferAutoRenewQuery;

final class DbalOfferAutoRenewQuery implements OfferAutoRenewQuery
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function countRenewsLeft(string $offerId) : int
    {
        return (int) $this->connection->createQueryBuilder()
            ->select('COUNT(oar.*)')
            ->from('itof_offer_auto_renew', 'oar')
            ->where('oar.offer_id = :offerId')
            ->andWhere('oar.renewed_at IS NULL')
            ->setParameters(
                [
                    'offerId' => $offerId,
                ]
            )->execute()
        ->fetchColumn();
    }

    public function countUsedRenews(string $offerId) : int
    {
        return (int) $this->connection->createQueryBuilder()
            ->select('COUNT(oar.*)')
            ->from('itof_offer_auto_renew', 'oar')
            ->where('oar.offer_id = :offerId')
            ->andWhere('oar.renewed_at IS NOT NULL')
            ->setParameters(
                [
                    'offerId' => $offerId,
                ]
            )->execute()
            ->fetchColumn();
    }

    public function countTotalRenews(string $offerId) : int
    {
        return (int) $this->connection->createQueryBuilder()
            ->select('COUNT(oar.*)')
            ->from('itof_offer_auto_renew', 'oar')
            ->where('oar.offer_id = :offerId')
            ->setParameters(
                [
                    'offerId' => $offerId,
                ]
            )->execute()
            ->fetchColumn();
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

    public function findUnassignedClosesToExpire(string $userId) : ?UnassignedAutoRenew
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

        return new UnassignedAutoRenew(
            $firstExtraOffer['user_id'],
            new \DateTimeImmutable($firstExtraOffer['expires_at'])
        );
    }

    public function findAllToRenew() : array
    {
        $offerAutoRenewsData = $this->connection->createQueryBuilder()
            ->select('oar.*')
            ->from('itof_offer_auto_renew', 'oar')
            ->andWhere('oar.renew_after <= NOW()')
            ->andWhere('oar.renewed_at IS NULL')
            ->andWhere('oar.offer_id IS NOT NULL')
            ->orderBy('oar.expires_at', 'ASC')
            ->execute()
            ->fetchAll();

        return \array_map(
            fn (array $offerAutoRenewData) : OfferAutoRenew => new OfferAutoRenew($offerAutoRenewData['offer_id']),
            $offerAutoRenewsData
        );
    }
}

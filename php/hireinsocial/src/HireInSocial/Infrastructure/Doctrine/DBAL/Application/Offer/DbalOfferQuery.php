<?php

declare(strict_types=1);

namespace HireInSocial\Infrastructure\Doctrine\DBAL\Application\Offer;

use Doctrine\DBAL\Connection;
use HireInSocial\Application\Query\Offer\Model\Offer;
use HireInSocial\Application\Query\Offer\Model\Offers;
use HireInSocial\Application\Query\Offer\OfferFilter;
use HireInSocial\Application\Query\Offer\OfferQuery;
use Ramsey\Uuid\Uuid;

final class DbalOfferQuery implements OfferQuery
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function total(): int
    {
        return (int) $this->connection->fetchColumn('SELECT COUNT(*) FROM his_job_offer');
    }

    public function find(OfferFilter $filter): Offers
    {
        $offersData = $this->connection->createQueryBuilder()
            ->select('o.*')
            ->from('his_job_offer', 'o')
            ->leftJoin('o', 'his_specialization', 's', 'o.specialization_id = s.id')
            ->where('s.slug = :specializationSlug AND o.created_at >= :sinceDate AND o.created_at <= :tillDate')
            ->orderBy('o.created_at', 'DESC')
            ->setMaxResults($filter->limit())
            ->setFirstResult($filter->offset())
            ->setParameters(
                [
                    'specializationSlug' => $filter->specialization(),
                    'sinceDate' => $filter->sinceDate()->format('Y-m-d H:i:s'),
                    'tillDate' => $filter->tillDate()->format('Y-m-d H:i:s'),
                ]
            )->execute()
            ->fetchAll();

        return new Offers(...\array_map(
            [$this, 'hydrateOffer'],
            $offersData
        ));
    }

    public function count(OfferFilter $filter): int
    {
        return (int) $this->connection->createQueryBuilder()
            ->select('COUNT(o.id)')
            ->from('his_job_offer', 'o')
            ->leftJoin('o', 'his_specialization', 's', 'o.specialization_id = s.id')
            ->where('s.slug = :specializationSlug AND o.created_at >= :sinceDate AND o.created_at <= :tillDate')
            ->setParameters(
                [
                    'specializationSlug' => $filter->specialization(),
                    'sinceDate' => $filter->sinceDate()->format('Y-m-d H:i:s'),
                    'tillDate' => $filter->tillDate()->format('Y-m-d H:i:s'),
                ]
            )
            ->execute()
            ->fetchColumn();
    }


    private function hydrateOffer(array $offerData) : Offer
    {
        $salary = $offerData['salary'] ? \json_decode($offerData['salary'], true) : null;

        return new Offer(
            Uuid::fromString($offerData['id']),
            new \DateTimeImmutable($offerData['created_at']),
            new Offer\Company($offerData['company_name'], $offerData['company_url'], $offerData['company_description']),
            new Offer\Contact($offerData['contact_email'], $offerData['contact_name'], $offerData['contact_phone']),
            new Offer\Contract($offerData['contract_type']),
            new Offer\Description($offerData['description_requirements'], $offerData['description_benefits']),
            new Offer\Location($offerData['location_remote'], $offerData['location_name']),
            new Offer\Position($offerData['position_name'], $offerData['position_description']),
            ($salary)
                ? new Offer\Salary(
                    $salary['min'],
                    $salary['max'],
                    $salary['currency_code'],
                    $salary['net']
                )
                : null
        );
    }
}

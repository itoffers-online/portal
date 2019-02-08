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
        $query = <<<SQL
            SELECT 
                *
            FROM his_job_offer o
            LEFT JOIN his_specialization s ON o.specialization_id = s.id
            WHERE s.slug = :specializationSlug
            ORDER BY o.created_at DESC
            OFFSET :offset
            LIMIT :limit
SQL;

        $offersData = $this->connection->fetchAll(
            $query,
            [
                'specializationSlug' => $filter->specialization(),
                'offset' => $filter->offset(),
                'limit' => $filter->limit(),
            ]
        );

        return new Offers(...\array_map(
            [$this, 'hydrateOffer'],
            $offersData
        ));
    }

    public function count(OfferFilter $filter): int
    {
        $query = <<<SQL
            SELECT 
                COUNT(o.id)
            FROM his_job_offer o
            LEFT JOIN his_specialization s ON o.specialization_id = s.id
            WHERE s.slug = :specializationSlug
SQL;

        return (int) $this->connection->fetchColumn(
            $query,
            [
                'specializationSlug' => $filter->specialization(),
            ]
        );
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

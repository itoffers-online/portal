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

use Doctrine\DBAL\Connection;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Company;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Contact;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Contract;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Description;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Location;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\OfferPDF;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Parameters;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Position;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Salary;
use HireInSocial\Offers\Application\Query\Offer\Model\Offers;
use HireInSocial\Offers\Application\Query\Offer\OfferFilter;
use HireInSocial\Offers\Application\Query\Offer\OfferQuery;
use Ramsey\Uuid\Uuid;

final class DbalOfferQuery implements OfferQuery
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function total() : int
    {
        return (int) $this->connection->fetchColumn('SELECT COUNT(*) FROM his_job_offer o WHERE o.removed_at IS NULL');
    }

    public function findAll(OfferFilter $filter) : Offers
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('
                o.*, 
                op.path as offer_pdf, 
                os.slug, 
                s.slug as specialization_slug, 
                CAST(o.salary->>\'max\' as INTEGER) as salary_max,
                (SELECT COUNT(a.*) FROM his_job_offer_application a WHERE o.id = a.offer_id) as applications_count
            ')
            ->from('his_job_offer', 'o')
            ->leftJoin('o', 'his_specialization', 's', 'o.specialization_id = s.id')
            ->leftJoin('o', 'his_job_offer_slug', 'os', 'os.offer_id = o.id')
            ->leftJoin('o', 'his_job_offer_pdf', 'op', 'op.offer_id = o.id')
            ->where('o.removed_at IS NULL');

        if ($filter->specialization()) {
            $queryBuilder->andWhere('s.slug = :specializationSlug');
        }

        if ($filter->remote()) {
            $queryBuilder->andWhere('o.location_remote = true');
        }

        if ($filter->withSalary()) {
            $queryBuilder->andWhere('o.salary IS NOT NULL');
        }

        if ($filter->userId()) {
            $queryBuilder->andWhere('o.user_id = :userId');
            $queryBuilder->setParameter('userId', $filter->userId());
        }

        if ($filter->afterOfferId()) {
            $queryBuilder->andWhere('o.id <> :afterOfferId')
                ->setParameter('afterOfferId', $filter->afterOfferId());
            $queryBuilder->andWhere('o.created_at <= (SELECT created_at FROM his_job_offer WHERE id = :afterOfferId)');
        } else {
            $queryBuilder->andWhere('o.created_at >= :sinceDate')
                ->setParameter('sinceDate', $filter->sinceDate()->format($this->connection->getDatabasePlatform()->getDateTimeFormatString()));
        }

        $queryBuilder->setMaxResults($filter->limit());

        if ($filter->offset()) {
            $queryBuilder->setFirstResult($filter->offset());
        }

        if ($filter->isSorted()) {
            foreach ($filter->sortByColumns() as $column) {
                if ($column->is(OfferFilter::COLUMN_SALARY)) {
                    $queryBuilder->addOrderBy('salary_max', $column->direction());
                }

                if ($column->is(OfferFilter::COLUMN_CREATED_AT)) {
                    $queryBuilder->addOrderBy('o.created_at', $column->direction());
                }
            }
        } else {
            $queryBuilder->orderBy('o.created_at', 'DESC');
        }

        $queryBuilder->setParameter('specializationSlug', $filter->specialization());

        $offersData = $queryBuilder->execute()
            ->fetchAll();

        return new Offers(...\array_map(
            [$this, 'hydrateOffer'],
            $offersData
        ));
    }

    public function count(OfferFilter $filter) : int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(o.id)')
            ->from('his_job_offer', 'o')
            ->leftJoin('o', 'his_specialization', 's', 'o.specialization_id = s.id')
            ->where('o.removed_at IS NULL');

        if ($filter->specialization()) {
            $queryBuilder->andWhere('s.slug = :specializationSlug');
        }

        if ($filter->remote()) {
            $queryBuilder->andWhere('o.location_remote = true');
        }

        if ($filter->withSalary()) {
            $queryBuilder->andWhere('o.salary IS NOT NULL');
        }

        if ($filter->userId()) {
            $queryBuilder->andWhere('o.user_id = :userId');
            $queryBuilder->setParameter('userId', $filter->userId());
        }

        if ($filter->afterOfferId()) {
            $queryBuilder->andWhere('o.id <> :afterOfferId')
                ->setParameter('afterOfferId', $filter->afterOfferId());
            $queryBuilder->andWhere('o.created_at <= (SELECT created_at FROM his_job_offer WHERE id = :afterOfferId)');
        } else {
            $queryBuilder->andWhere('o.created_at >= :sinceDate')
                ->setParameter('sinceDate', $filter->sinceDate()->format($this->connection->getDatabasePlatform()->getDateTimeFormatString()));
        }

        $queryBuilder->setParameter('specializationSlug', $filter->specialization());

        return (int) $queryBuilder
            ->execute()
            ->fetchColumn();
    }

    public function findById(string $id) : ?Offer
    {
        $offerData = $this->connection->createQueryBuilder()
            ->select('
                o.*, 
                op.path as offer_pdf, 
                os.slug, 
                s.slug as specialization_slug,
                (SELECT COUNT(a.*) FROM his_job_offer_application a WHERE o.id = a.offer_id) as applications_count
            ')
            ->from('his_job_offer_slug', 'os')
            ->leftJoin('os', 'his_job_offer', 'o', 'os.offer_id = o.id')
            ->leftJoin('o', 'his_job_offer_pdf', 'op', 'op.offer_id = o.id')
            ->leftJoin('o', 'his_specialization', 's', 'o.specialization_id = s.id')
            ->where('o.id = :id')
            ->andWhere('o.removed_at IS NULL')
            ->setParameters(
                [
                    'id' => $id,
                ]
            )->execute()
            ->fetch();

        if (!$offerData) {
            return null;
        }

        return $this->hydrateOffer($offerData);
    }

    public function findByEmailHash(string $emailHah) : ?Offer
    {
        $offerData = $this->connection->createQueryBuilder()
            ->select('
                o.*, 
                os.slug, 
                s.slug as specialization_slug,
                (SELECT COUNT(a.*) FROM his_job_offer_application a WHERE o.id = a.offer_id) as applications_count
            ')
            ->from('his_job_offer_slug', 'os')
            ->leftJoin('os', 'his_job_offer', 'o', 'os.offer_id = o.id')
            ->leftJoin('o', 'his_job_offer_pdf', 'op', 'op.offer_id = o.id')
            ->leftJoin('o', 'his_specialization', 's', 'o.specialization_id = s.id')
            ->where('o.email_hash = :emailHash')
            ->andWhere('o.removed_at IS NULL')
            ->setParameters(
                [
                    'emailHash' => $emailHah,
                ]
            )->execute()
            ->fetch();

        if (!$offerData) {
            return null;
        }

        return $this->hydrateOffer($offerData);
    }

    public function findBySlug(string $slug) : ?Offer
    {
        $offerData = $this->connection->createQueryBuilder()
            ->select('
                o.*, 
                op.path as offer_pdf, 
                os.slug, 
                s.slug as specialization_slug,
                (SELECT COUNT(a.*) FROM his_job_offer_application a WHERE o.id = a.offer_id) as applications_count
            ')
            ->from('his_job_offer_slug', 'os')
            ->leftJoin('os', 'his_job_offer', 'o', 'os.offer_id = o.id')
            ->leftJoin('o', 'his_job_offer_pdf', 'op', 'op.offer_id = o.id')
            ->leftJoin('o', 'his_specialization', 's', 'o.specialization_id = s.id')
            ->where('os.slug = :offerSlug')
            ->andWhere('o.removed_at IS NULL')
            ->setParameters(
                [
                    'offerSlug' => $slug,
                ]
            )->execute()
            ->fetch();

        if (!$offerData) {
            return null;
        }

        return $this->hydrateOffer($offerData);
    }

    public function findOneAfter(Offer $offer) : ?Offer
    {
        $offerData = $this->connection->createQueryBuilder()
            ->select('
                o.*, 
                op.path as offer_pdf, 
                os.slug, 
                s.slug as specialization_slug,
                (SELECT COUNT(a.*) FROM his_job_offer_application a WHERE o.id = a.offer_id) as applications_count
            ')
            ->from('his_job_offer', 'o')
            ->leftJoin('o', 'his_specialization', 's', 'o.specialization_id = s.id')
            ->leftJoin('o', 'his_job_offer_slug', 'os', 'os.offer_id = o.id')
            ->leftJoin('o', 'his_job_offer_pdf', 'op', 'op.offer_id = o.id')
            ->where('s.slug = :specializationSlug')
            ->andWhere('o.created_at < (SELECT created_at FROM his_job_offer WHERE id = :previousOfferId)')
            ->andWhere('o.id <> :previousOfferId')
            ->andWhere('o.removed_at IS NULL')
            ->orderBy('o.created_at', 'DESC')
            ->setMaxResults(1)
            ->setParameters(
                [
                    'specializationSlug' => $offer->specializationSlug(),
                    'previousOfferId' => $offer->id()->toString(),
                ]
            )->execute()
            ->fetch();

        if (!$offerData) {
            return null;
        }

        return $this->hydrateOffer($offerData);
    }

    public function findOneBefore(Offer $offer) : ?Offer
    {
        $offerData = $this->connection->createQueryBuilder()
            ->select('
                o.*, 
                op.path as offer_pdf,
                os.slug, 
                s.slug as specialization_slug,
                (SELECT COUNT(a.*) FROM his_job_offer_application a WHERE o.id = a.offer_id) as applications_count
            ')
            ->from('his_job_offer', 'o')
            ->leftJoin('o', 'his_specialization', 's', 'o.specialization_id = s.id')
            ->leftJoin('o', 'his_job_offer_slug', 'os', 'os.offer_id = o.id')
            ->leftJoin('o', 'his_job_offer_pdf', 'op', 'op.offer_id = o.id')
            ->where('s.slug = :specializationSlug')
            ->andWhere('o.created_at > (SELECT created_at FROM his_job_offer WHERE id = :previousOfferId)')
            ->andWhere('o.id <> :previousOfferId')
            ->andWhere('o.removed_at IS NULL')
            ->orderBy('o.created_at', 'ASC')
            ->setMaxResults(1)
            ->setParameters(
                [
                    'specializationSlug' => $offer->specializationSlug(),
                    'previousOfferId' => $offer->id()->toString(),
                ]
            )->execute()
            ->fetch();

        if (!$offerData) {
            return null;
        }

        return $this->hydrateOffer($offerData);
    }

    private function hydrateOffer(array $offerData) : Offer
    {
        $salary = isset($offerData['salary']) ? \json_decode($offerData['salary'], true) : null;
        $offerPDF = isset($offerData['offer_pdf']) ? new OfferPDF($offerData['offer_pdf']) : null;

        return new Offer(
            Uuid::fromString($offerData['id']),
            $offerData['slug'],
            $offerData['email_hash'],
            Uuid::fromString($offerData['user_id']),
            $offerData['specialization_slug'],
            new \DateTimeImmutable($offerData['created_at']),
            new Parameters(
                new Company($offerData['company_name'], $offerData['company_url'], $offerData['company_description']),
                new Contact($offerData['contact_email'], $offerData['contact_name'], $offerData['contact_phone']),
                new Contract($offerData['contract_type']),
                new Description($offerData['description_requirements'], $offerData['description_benefits']),
                new Location(
                    $offerData['location_remote'],
                    $offerData['location_name'],
                    $offerData['location_lat'] ? (float) $offerData['location_lat'] : null,
                    $offerData['location_lng'] ? (float) $offerData['location_lng'] : null
                ),
                new Position($offerData['position_name'], $offerData['position_description']),
                ($salary)
                    ? new Salary(
                        $salary['min'],
                        $salary['max'],
                        $salary['currency_code'],
                        $salary['net'],
                        $salary['period_type']
                    )
                    : null
            ),
            $offerData['applications_count'],
            $offerPDF
        );
    }
}

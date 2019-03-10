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

namespace HireInSocial\Infrastructure\Doctrine\DBAL\Application\Specialization;

use Doctrine\DBAL\Connection;
use HireInSocial\Application\Query\Specialization\Model\Specialization;
use HireInSocial\Application\Query\Specialization\Model\Specialization\FacebookChannel;
use HireInSocial\Application\Query\Specialization\Model\Specializations;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;

final class DbalSpecializationQuery implements SpecializationQuery
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function all(): Specializations
    {
        return new Specializations(
            ...\array_map(
                [$this, 'hydrateSpecialization'],
                $this->connection->fetchAll(
                    <<<SQL
                  SELECT 
                     s.id,
                     s.slug, 
                     s.facebook_channel_page_id as fb_page_id, 
                     s.facebook_channel_group_id as fb_group_id 
                  FROM his_specialization s 
                  ORDER BY s.slug
SQL
                )
            )
        );
    }

    public function findBySlug(string $slug): ?Specialization
    {
        $specialization = $this->connection->fetchAssoc(
            <<<SQL
            SELECT 
               s.id,
               s.slug, 
               s.facebook_channel_page_id as fb_page_id, 
               s.facebook_channel_group_id as fb_group_id
            FROM his_specialization s
            WHERE s.slug = :slug
SQL
            ,
            ['slug' => $slug]
        );

        if (!$specialization) {
            return null;
        }

        return $this->hydrateSpecialization($specialization);
    }

    public function hydrateSpecialization(array $data): Specialization
    {
        // TODO: Optimize this, maybe try to merge this into main query or migrate to projections
        $offersData = $this->connection->fetchAssoc(
            <<<SQL
            SELECT 
               (SELECT COUNT(*) FROM his_job_offer  WHERE specialization_id = :specializationId) as total_count,
               o.created_at
            FROM his_job_offer o 
            WHERE o.specialization_id = :specializationId
            ORDER BY o.created_at DESC LIMIT 1
SQL
            ,
            ['specializationId' => $data['id']]
        );

        $offers = $offersData
            ?  Specialization\Offers::create(
                $offersData['total_count'],
                new \DateTimeImmutable($offersData['created_at'])
            )
            : Specialization\Offers::noOffers();

        return new Specialization(
            $data['slug'],
            $offers,
            ($data['fb_page_id'])
                ? new FacebookChannel(
                   $data['fb_page_id'],
                    $data['fb_group_id']
                )
                : null
        );
    }
}

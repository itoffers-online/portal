<?php

declare (strict_types=1);

namespace HireInSocial\Infrastructure\Doctrine\DBAL\Application\Specialization;

use Doctrine\DBAL\Connection;
use HireInSocial\Application\Specialization\Specialization;
use HireInSocial\Application\Specialization\Specializations;

final class DBALSpecializations implements Specializations
{
    public const TABLE_NAME = 'his_specialization';
    public const FIELD_SLUG = 'slug';
    public const FIELD_NAME = 'name';
    public const FIELD_FACEBOOK_PAGE_ID = 'facebook_page_id';
    public const FIELD_FACEBOOK_GROUP_ID = 'facebook_group_id';

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function add(Specialization $specialization): void
    {
        $reflection = new \ReflectionClass(Specialization::class);

        $reflection->getProperty('slug')->setAccessible(true);

        $this->connection->insert(
            self::TABLE_NAME,
            [
                self::FIELD_SLUG => $reflection->getProperty('slug')->getValue($specialization),
                self::TABLE_NAME => $reflection->getProperty('name')->getValue($specialization),
            ],
            [
                self::FIELD_CREATED_AT => 'datetime_immutable',
                self::FIELD_SALARY_NET => 'boolean',
                self::FIELD_LOCATION_REMOTE => 'boolean',
            ]
        );
    }

    public function get(string $slug): Specialization
    {
        // TODO: Implement get() method.
    }
}
<?php

declare (strict_types=1);

namespace HireInSocial\Infrastructure\Doctrine\DBAL\Schema;

use Doctrine\DBAL\Migrations\Provider\SchemaProviderInterface;
use Doctrine\DBAL\Schema\Schema;
use HireInSocial\Infrastructure\Doctrine\DBAL\Application\Facebook\DBALPosts;
use HireInSocial\Infrastructure\Doctrine\DBAL\Application\Offer\DBALOffers;

final class HireInSocialSchemaProvider implements SchemaProviderInterface
{
    public function createSchema()
    {
        $schema = new Schema();

        $schema->createNamespace('public');

        $this->createJobOfferTable($schema);
        $this->createFacebookPostTable($schema);

        return $schema;
    }

    /**
     * @param Schema $schema
     */
    private function createJobOfferTable(Schema $schema): void
    {
        $table = $schema->createTable(DBALOffers::TABLE_NAME);
        $table->addColumn(DBALOffers::FIELD_ID, 'guid', [
            'notnull' => true,
        ]);
        $table->setPrimaryKey([DBALOffers::FIELD_ID]);

        $table->addColumn(DBALOffers::FIELD_CREATED_AT, 'datetime_immutable', [
            'notnull' => true
        ]);

        $table->addColumn(DBALOffers::FIELD_COMPANY_NAME, 'string', [
            'length' => 255,
            'notnull' => true
        ]);

        $table->addColumn(DBALOffers::FIELD_COMPANY_URL, 'string', [
            'length' => 2083,
            'notnull' => true
        ]);

        $table->addColumn(DBALOffers::FIELD_COMPANY_DESCRIPTION, 'string', [
            'length' => 512,
            'notnull' => true
        ]);

        $table->addColumn(DBALOffers::FIELD_POSITION_NAME, 'string', [
            'length' => 255,
            'notnull' => true
        ]);

        $table->addColumn(DBALOffers::FIELD_POSITION_DESCRIPTION, 'string', [
            'length' => 1024,
            'notnull' => true
        ]);

        $table->addColumn(DBALOffers::FIELD_LOCATION_REMOTE, 'boolean', [
            'notnull' => true
        ]);

        $table->addColumn(DBALOffers::FIELD_LOCATION_NAME, 'string', [
            'notnull' => false,
            'length' => 512
        ]);

        $table->addColumn(DBALOffers::FIELD_SALARY_MIN, 'integer', [
            'notnull' => true,
        ]);

        $table->addColumn(DBALOffers::FIELD_SALARY_MAX, 'integer', [
            'notnull' => true,
        ]);

        $table->addColumn(DBALOffers::FIELD_SALARY_CURRENCY, 'string', [
            'notnull' => true,
            'length' => 3
        ]);

        $table->addColumn(DBALOffers::FIELD_SALARY_NET, 'boolean', [
            'notnull' => true
        ]);

        $table->addColumn(DBALOffers::FIELD_CONTRACT_TYPE, 'string', [
            'notnull' => true,
            'length' => 255
        ]);

        $table->addColumn(DBALOffers::FIELD_DESCRIPTION_REQUIREMENTS, 'string', [
            'notnull' => true,
            'length' => 1024
        ]);

        $table->addColumn(DBALOffers::FIELD_DESCRIPTION_BENEFITS, 'string', [
            'notnull' => true,
            'length' => 1024
        ]);

        $table->addColumn(DBALOffers::FIELD_CONTACT_EMAIL, 'string', [
            'notnull' => true,
            'length' => 255
        ]);

        $table->addColumn(DBALOffers::FIELD_CONTACT_NAME, 'string', [
            'notnull' => true,
            'length' => 255
        ]);

        $table->addColumn(DBALOffers::FIELD_CONTACT_PHONE, 'string', [
            'notnull' => false,
            'length' => 16
        ]);
    }

    private function createFacebookPostTable(Schema $schema): void
    {
        $table = $schema->createTable(DBALPosts::TABLE_NAME);
        $table->addColumn(DBALPosts::FIELD_FB_ID, 'string', [
            'notnull' => true,
        ]);
        $table->setPrimaryKey([DBALPosts::FIELD_FB_ID]);

        $table->addColumn(DBALPosts::FIELD_JOB_OFFER_ID, 'guid', [
            'notnull' => true,
        ]);
        $table->addColumn(DBALPosts::FIELD_FB_AUTHOR_ID, 'string', [
            'notnull' => true,
            'length' => 255
        ]);
    }
}
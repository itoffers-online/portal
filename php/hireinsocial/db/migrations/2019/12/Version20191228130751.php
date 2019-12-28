<?php declare(strict_types=1);

namespace HireInSocial\Offers\Application\System\Doctrine\DBAL\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191228130751 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE his_specialization (id UUID NOT NULL, slug VARCHAR(255) NOT NULL, facebook_channel_page_id VARCHAR(255) DEFAULT NULL, facebook_channel_page_access_token VARCHAR(255) DEFAULT NULL, facebook_channel_group_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E60FD12B989D9B62 ON his_specialization (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E60FD12BA719CCF6 ON his_specialization (facebook_channel_group_id)');
        $this->addSql('CREATE TABLE his_job_offer_pdf (id UUID NOT NULL, offer_id UUID NOT NULL, path VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN his_job_offer_pdf.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE his_job_offer_application (id UUID NOT NULL, offer_id UUID NOT NULL, email_hash VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN his_job_offer_application.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE his_job_offer_slug (slug TEXT NOT NULL, offer_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(slug))');
        $this->addSql('COMMENT ON COLUMN his_job_offer_slug.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE his_user (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, blocked_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, fb_user_app_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E3204371F2B69CCC ON his_user (fb_user_app_id)');
        $this->addSql('COMMENT ON COLUMN his_user.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN his_user.blocked_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE his_facebook_post (fb_id VARCHAR(255) NOT NULL, job_offer_id UUID NOT NULL, PRIMARY KEY(fb_id))');
        $this->addSql('CREATE TABLE his_job_offer (id UUID NOT NULL, email_hash VARCHAR(255) NOT NULL, user_id UUID NOT NULL, specialization_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, salary JSON DEFAULT NULL, removed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, company_name VARCHAR(255) NOT NULL, company_url VARCHAR(2083) NOT NULL, company_description VARCHAR(512) NOT NULL, position_name VARCHAR(255) NOT NULL, position_description VARCHAR(1024) NOT NULL, location_remote BOOLEAN NOT NULL, location_name VARCHAR(512) DEFAULT NULL, location_lat DOUBLE PRECISION DEFAULT NULL, location_lng DOUBLE PRECISION DEFAULT NULL, contract_type VARCHAR(255) NOT NULL, description_requirements VARCHAR(1024) NOT NULL, description_benefits VARCHAR(1024) NOT NULL, contact_email VARCHAR(255) NOT NULL, contact_name VARCHAR(255) NOT NULL, contact_phone VARCHAR(16) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN his_job_offer.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN his_job_offer.salary IS \'(DC2Type:his_offer_salary)\'');
        $this->addSql('COMMENT ON COLUMN his_job_offer.removed_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE his_specialization');
        $this->addSql('DROP TABLE his_job_offer_pdf');
        $this->addSql('DROP TABLE his_job_offer_application');
        $this->addSql('DROP TABLE his_job_offer_slug');
        $this->addSql('DROP TABLE his_user');
        $this->addSql('DROP TABLE his_facebook_post');
        $this->addSql('DROP TABLE his_job_offer');
    }
}

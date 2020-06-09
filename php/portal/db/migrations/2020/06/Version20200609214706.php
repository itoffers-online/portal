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

namespace ITOffers\Doctrine\DBAL\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200609214706 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('COMMENT ON COLUMN itof_job_offer_pdf.created_at IS \'(DC2Type:aeon_datetime)\'');
        $this->addSql('COMMENT ON COLUMN itof_job_offer_application.created_at IS \'(DC2Type:aeon_datetime)\'');
        $this->addSql('COMMENT ON COLUMN itof_job_offer_slug.created_at IS \'(DC2Type:aeon_datetime)\'');
        $this->addSql('COMMENT ON COLUMN itof_user.created_at IS \'(DC2Type:aeon_datetime)\'');
        $this->addSql('COMMENT ON COLUMN itof_user.blocked_at IS \'(DC2Type:aeon_datetime)\'');
        $this->addSql('COMMENT ON COLUMN itof_offer_auto_renew.expires_at IS \'(DC2Type:aeon_datetime)\'');
        $this->addSql('COMMENT ON COLUMN itof_offer_auto_renew.created_at IS \'(DC2Type:aeon_datetime)\'');
        $this->addSql('COMMENT ON COLUMN itof_offer_auto_renew.renew_after IS \'(DC2Type:aeon_datetime)\'');
        $this->addSql('COMMENT ON COLUMN itof_offer_auto_renew.renewed_at IS \'(DC2Type:aeon_datetime)\'');
        $this->addSql('COMMENT ON COLUMN itof_extra_offer.created_at IS \'(DC2Type:aeon_datetime)\'');
        $this->addSql('COMMENT ON COLUMN itof_extra_offer.expires_at IS \'(DC2Type:aeon_datetime)\'');
        $this->addSql('COMMENT ON COLUMN itof_extra_offer.used_at IS \'(DC2Type:aeon_datetime)\'');
        $this->addSql('COMMENT ON COLUMN itof_job_offer.created_at IS \'(DC2Type:aeon_datetime)\'');
        $this->addSql('COMMENT ON COLUMN itof_job_offer.removed_at IS \'(DC2Type:aeon_datetime)\'');
        $this->addSql('COMMENT ON COLUMN itof_job_offer.updated_at IS \'(DC2Type:aeon_datetime)\'');
        $this->addSql('COMMENT ON COLUMN itof_job_offer_company_logo.created_at IS \'(DC2Type:aeon_datetime)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('COMMENT ON COLUMN itof_job_offer_pdf.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN itof_job_offer_application.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN itof_job_offer_slug.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN itof_user.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN itof_user.blocked_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN itof_offer_auto_renew.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN itof_offer_auto_renew.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN itof_offer_auto_renew.renew_after IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN itof_offer_auto_renew.renewed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN itof_extra_offer.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN itof_extra_offer.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN itof_extra_offer.used_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN itof_job_offer.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN itof_job_offer.removed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN itof_job_offer.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN itof_job_offer_company_logo.created_at IS \'(DC2Type:datetime_immutable)\'');
    }
}

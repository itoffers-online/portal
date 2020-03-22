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
final class Version20200322180735 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE itof_job_offer ALTER company_description TYPE TEXT');
        $this->addSql('ALTER TABLE itof_job_offer ALTER company_description DROP DEFAULT');
        $this->addSql('ALTER TABLE itof_job_offer ALTER company_description TYPE TEXT');
        $this->addSql('ALTER TABLE itof_job_offer ALTER description_technology_stack TYPE TEXT');
        $this->addSql('ALTER TABLE itof_job_offer ALTER description_technology_stack DROP DEFAULT');
        $this->addSql('ALTER TABLE itof_job_offer ALTER description_technology_stack TYPE TEXT');
        $this->addSql('ALTER TABLE itof_job_offer ALTER description_benefits TYPE TEXT');
        $this->addSql('ALTER TABLE itof_job_offer ALTER description_benefits DROP DEFAULT');
        $this->addSql('ALTER TABLE itof_job_offer ALTER description_benefits TYPE TEXT');
        $this->addSql('ALTER TABLE itof_job_offer ALTER description_requirements_description TYPE TEXT');
        $this->addSql('ALTER TABLE itof_job_offer ALTER description_requirements_description DROP DEFAULT');
        $this->addSql('ALTER TABLE itof_job_offer ALTER description_requirements_description TYPE TEXT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE itof_job_offer ALTER company_description TYPE VARCHAR(2048)');
        $this->addSql('ALTER TABLE itof_job_offer ALTER company_description DROP DEFAULT');
        $this->addSql('ALTER TABLE itof_job_offer ALTER description_technology_stack TYPE VARCHAR(2048)');
        $this->addSql('ALTER TABLE itof_job_offer ALTER description_technology_stack DROP DEFAULT');
        $this->addSql('ALTER TABLE itof_job_offer ALTER description_benefits TYPE VARCHAR(2048)');
        $this->addSql('ALTER TABLE itof_job_offer ALTER description_benefits DROP DEFAULT');
        $this->addSql('ALTER TABLE itof_job_offer ALTER description_requirements_description TYPE VARCHAR(2048)');
        $this->addSql('ALTER TABLE itof_job_offer ALTER description_requirements_description DROP DEFAULT');
    }
}

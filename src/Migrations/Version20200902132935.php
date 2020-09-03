<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200902132935 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE personal_licencia ADD legajo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE personal_licencia ADD fecha_reincorporacion DATE NOT NULL');
        $this->addSql('ALTER TABLE personal_licencia ADD CONSTRAINT FK_90274C42602BF2CE FOREIGN KEY (legajo_id) REFERENCES personal_legajo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_90274C42602BF2CE ON personal_licencia (legajo_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE personal_licencia DROP CONSTRAINT FK_90274C42602BF2CE');
        $this->addSql('DROP INDEX IDX_90274C42602BF2CE');
        $this->addSql('ALTER TABLE personal_licencia DROP legajo_id');
        $this->addSql('ALTER TABLE personal_licencia DROP fecha_reincorporacion');
    }
}

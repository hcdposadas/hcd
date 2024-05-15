<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221023225700 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE paciente ADD observaciones TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE paciente ADD foto VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE personal_articulo ADD dias INT DEFAULT NULL');
        $this->addSql('ALTER TABLE persona ADD paciente_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE persona ADD CONSTRAINT FK_51E5B69B7310DAD4 FOREIGN KEY (paciente_id) REFERENCES paciente (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_51E5B69B7310DAD4 ON persona (paciente_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE persona DROP CONSTRAINT FK_51E5B69B7310DAD4');
        $this->addSql('DROP INDEX UNIQ_51E5B69B7310DAD4');
        $this->addSql('ALTER TABLE persona DROP paciente_id');
        $this->addSql('ALTER TABLE paciente DROP observaciones');
        $this->addSql('ALTER TABLE paciente DROP foto');
        $this->addSql('ALTER TABLE personal_articulo DROP dias');
    }
}

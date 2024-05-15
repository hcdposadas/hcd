<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221023221617 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE paciente_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE orden_medica_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE paciente (id INT NOT NULL, persona_id INT NOT NULL, grupo VARCHAR(10) DEFAULT NULL, factor VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C6CBA95EF5F88DB9 ON paciente (persona_id)');
        $this->addSql('CREATE TABLE orden_medica (id INT NOT NULL, paciente_id INT NOT NULL, articulo_id INT NOT NULL, medico_otorgante VARCHAR(255) NOT NULL, desde DATE NOT NULL, hasta DATE NOT NULL, diagnostico VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3BC688FA7310DAD4 ON orden_medica (paciente_id)');
        $this->addSql('CREATE INDEX IDX_3BC688FA2DBC2FC9 ON orden_medica (articulo_id)');
        $this->addSql('ALTER TABLE paciente ADD CONSTRAINT FK_C6CBA95EF5F88DB9 FOREIGN KEY (persona_id) REFERENCES persona (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orden_medica ADD CONSTRAINT FK_3BC688FA7310DAD4 FOREIGN KEY (paciente_id) REFERENCES paciente (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE orden_medica ADD CONSTRAINT FK_3BC688FA2DBC2FC9 FOREIGN KEY (articulo_id) REFERENCES personal_articulo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE orden_medica DROP CONSTRAINT FK_3BC688FA7310DAD4');
        $this->addSql('DROP SEQUENCE paciente_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE orden_medica_id_seq CASCADE');
        $this->addSql('DROP TABLE paciente');
        $this->addSql('DROP TABLE orden_medica');
    }
}

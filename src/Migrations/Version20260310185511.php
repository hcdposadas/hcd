<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260310185511 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE comunicacion_archivo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE comunicacion_archivo (id INT NOT NULL, comunicacion_id INT NOT NULL, archivo VARCHAR(255) DEFAULT NULL, nombre_original VARCHAR(255) DEFAULT NULL, fecha_subida TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C91C167D312EA0C2 ON comunicacion_archivo (comunicacion_id)');
        $this->addSql('ALTER TABLE comunicacion_archivo ADD CONSTRAINT FK_C91C167D312EA0C2 FOREIGN KEY (comunicacion_id) REFERENCES comunicacion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE comunicacion_archivo_id_seq CASCADE');
        $this->addSql('DROP TABLE comunicacion_archivo');
    }
}

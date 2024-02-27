<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240226175722 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE recibido_comunicado_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE recibido_comunicado (id INT NOT NULL, comunicacion_id INT NOT NULL, area_id INT NOT NULL, estado VARCHAR(255) NOT NULL, fecha TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F08D4DE2312EA0C2 ON recibido_comunicado (comunicacion_id)');
        $this->addSql('CREATE INDEX IDX_F08D4DE2BD0F409C ON recibido_comunicado (area_id)');
        $this->addSql('ALTER TABLE recibido_comunicado ADD CONSTRAINT FK_F08D4DE2312EA0C2 FOREIGN KEY (comunicacion_id) REFERENCES comunicacion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE recibido_comunicado ADD CONSTRAINT FK_F08D4DE2BD0F409C FOREIGN KEY (area_id) REFERENCES area_administrativa (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE recibido_comunicado_id_seq CASCADE');
        $this->addSql('DROP TABLE recibido_comunicado');
    }
}

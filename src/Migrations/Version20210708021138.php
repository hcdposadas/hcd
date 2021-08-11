<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210708021138 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE proyecto_firmado_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE proyecto_firmado (id INT NOT NULL, expediente_id INT NOT NULL, creado_por_id INT DEFAULT NULL, actualizado_por_id INT DEFAULT NULL, archivo VARCHAR(255) NOT NULL, activo BOOLEAN NOT NULL, fecha_creacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fecha_actualizacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EBDC775F4BF37E4E ON proyecto_firmado (expediente_id)');
        $this->addSql('CREATE INDEX IDX_EBDC775FFE35D8C4 ON proyecto_firmado (creado_por_id)');
        $this->addSql('CREATE INDEX IDX_EBDC775FF6167A1C ON proyecto_firmado (actualizado_por_id)');
        $this->addSql('ALTER TABLE proyecto_firmado ADD CONSTRAINT FK_EBDC775F4BF37E4E FOREIGN KEY (expediente_id) REFERENCES expediente (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE proyecto_firmado ADD CONSTRAINT FK_EBDC775FFE35D8C4 FOREIGN KEY (creado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE proyecto_firmado ADD CONSTRAINT FK_EBDC775FF6167A1C FOREIGN KEY (actualizado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE periodo_legislativo ALTER frase TYPE VARCHAR(500)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE proyecto_firmado_id_seq CASCADE');
        $this->addSql('DROP TABLE proyecto_firmado');
        $this->addSql('ALTER TABLE periodo_legislativo ALTER frase TYPE VARCHAR(255)');
    }
}

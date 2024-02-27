<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240226141532 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE comunicacion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE comunicacion (id INT NOT NULL, area_origen_id INT NOT NULL, numero INT NOT NULL, anio INT NOT NULL, fecha TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, estado VARCHAR(255) NOT NULL, archivo VARCHAR(255) NOT NULL, tipo VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AE9F18F062EDB11 ON comunicacion (area_origen_id)');
        $this->addSql('CREATE TABLE comunicacion_area_administrativa (comunicacion_id INT NOT NULL, area_administrativa_id INT NOT NULL, PRIMARY KEY(comunicacion_id, area_administrativa_id))');
        $this->addSql('CREATE INDEX IDX_446067B3312EA0C2 ON comunicacion_area_administrativa (comunicacion_id)');
        $this->addSql('CREATE INDEX IDX_446067B32EAC5EB1 ON comunicacion_area_administrativa (area_administrativa_id)');
        $this->addSql('ALTER TABLE comunicacion ADD CONSTRAINT FK_AE9F18F062EDB11 FOREIGN KEY (area_origen_id) REFERENCES area_administrativa (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comunicacion_area_administrativa ADD CONSTRAINT FK_446067B3312EA0C2 FOREIGN KEY (comunicacion_id) REFERENCES comunicacion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comunicacion_area_administrativa ADD CONSTRAINT FK_446067B32EAC5EB1 FOREIGN KEY (area_administrativa_id) REFERENCES area_administrativa (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE comunicacion_area_administrativa DROP CONSTRAINT FK_446067B3312EA0C2');
        $this->addSql('DROP SEQUENCE comunicacion_id_seq CASCADE');
        $this->addSql('DROP TABLE comunicacion');
        $this->addSql('DROP TABLE comunicacion_area_administrativa');
    }
}

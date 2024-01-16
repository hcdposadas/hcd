<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240107230103 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE ticket_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ticket (id INT NOT NULL, area_origen_id INT NOT NULL, area_destino_id INT NOT NULL, texto VARCHAR(255) NOT NULL, completo BOOLEAN NOT NULL, observacion VARCHAR(255) DEFAULT NULL, fecha TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_97A0ADA362EDB11 ON ticket (area_origen_id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA36BC6E4AF ON ticket (area_destino_id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA362EDB11 FOREIGN KEY (area_origen_id) REFERENCES area_administrativa (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA36BC6E4AF FOREIGN KEY (area_destino_id) REFERENCES area_administrativa (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE giro_administrativo ADD estado VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE persona DROP CONSTRAINT fk_51e5b69b7310dad4');
        $this->addSql('DROP INDEX uniq_51e5b69b7310dad4');
        $this->addSql('ALTER TABLE persona DROP paciente_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE ticket_id_seq CASCADE');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('ALTER TABLE giro_administrativo DROP estado');
        $this->addSql('ALTER TABLE persona ADD paciente_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE persona ADD CONSTRAINT fk_51e5b69b7310dad4 FOREIGN KEY (paciente_id) REFERENCES paciente (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_51e5b69b7310dad4 ON persona (paciente_id)');
    }
}

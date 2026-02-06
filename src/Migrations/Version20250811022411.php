<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250811022411 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE no_conformidad_adjunto_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE no_conformidad_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE no_conformidad_adjunto (id INT NOT NULL, no_conformidad_id INT NOT NULL, archivo VARCHAR(255) DEFAULT NULL, nombre_original VARCHAR(255) DEFAULT NULL, fecha_subida TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_714A6E7087A3B2F6 ON no_conformidad_adjunto (no_conformidad_id)');
        $this->addSql('CREATE TABLE no_conformidad (id INT NOT NULL, area_id INT NOT NULL, empleado_id INT NOT NULL, asignado_a_id INT DEFAULT NULL, creado_por_id INT DEFAULT NULL, origen VARCHAR(50) NOT NULL, categoria VARCHAR(50) NOT NULL, requisito TEXT DEFAULT NULL, descripcion_hallazgo TEXT DEFAULT NULL, fecha TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, imagen VARCHAR(255) DEFAULT NULL, documento VARCHAR(255) DEFAULT NULL, estado VARCHAR(50) NOT NULL, responsable_calidad VARCHAR(255) DEFAULT NULL, correccion TEXT DEFAULT NULL, fecha_correccion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, analisis_causa TEXT DEFAULT NULL, accion_correctiva TEXT DEFAULT NULL, fecha_accion_correctiva TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, correccion_verificada BOOLEAN DEFAULT NULL, efectividad_verificada BOOLEAN DEFAULT NULL, comentarios_verificacion TEXT DEFAULT NULL, analisis_aceptado BOOLEAN DEFAULT NULL, explicacion_revision_analisis TEXT DEFAULT NULL, explicacion_desestimado TEXT DEFAULT NULL, nueva_fecha_correccion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, norma VARCHAR(20) DEFAULT NULL, requisito_especifico VARCHAR(255) DEFAULT NULL, pgcd VARCHAR(255) DEFAULT NULL, requisito_legal BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FF11325EBD0F409C ON no_conformidad (area_id)');
        $this->addSql('CREATE INDEX IDX_FF11325E952BE730 ON no_conformidad (empleado_id)');
        $this->addSql('CREATE INDEX IDX_FF11325E39055ADD ON no_conformidad (asignado_a_id)');
        $this->addSql('CREATE INDEX IDX_FF11325EFE35D8C4 ON no_conformidad (creado_por_id)');
        $this->addSql('ALTER TABLE no_conformidad_adjunto ADD CONSTRAINT FK_714A6E7087A3B2F6 FOREIGN KEY (no_conformidad_id) REFERENCES no_conformidad (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE no_conformidad ADD CONSTRAINT FK_FF11325EBD0F409C FOREIGN KEY (area_id) REFERENCES area_administrativa (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE no_conformidad ADD CONSTRAINT FK_FF11325E952BE730 FOREIGN KEY (empleado_id) REFERENCES persona (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE no_conformidad ADD CONSTRAINT FK_FF11325E39055ADD FOREIGN KEY (asignado_a_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE no_conformidad ADD CONSTRAINT FK_FF11325EFE35D8C4 FOREIGN KEY (creado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER INDEX idx_97a0ada3ef08a5 RENAME TO IDX_97A0ADA3E45510D9');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE no_conformidad_adjunto DROP CONSTRAINT FK_714A6E7087A3B2F6');
        $this->addSql('DROP SEQUENCE no_conformidad_adjunto_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE no_conformidad_id_seq CASCADE');
        $this->addSql('DROP TABLE no_conformidad_adjunto');
        $this->addSql('DROP TABLE no_conformidad');
        $this->addSql('ALTER INDEX idx_97a0ada3e45510d9 RENAME TO idx_97a0ada3ef08a5');
    }
}

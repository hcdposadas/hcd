<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200708214638 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE personal_ddjjpersona_acargo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE personal_ddjjpersona_acargo (id INT NOT NULL, ddjj_id INT NOT NULL, personas_acargo_id INT NOT NULL, creado_por_id INT DEFAULT NULL, actualizado_por_id INT DEFAULT NULL, activo BOOLEAN NOT NULL, fecha_creacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fecha_actualizacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_142D503C6FE99E6D ON personal_ddjjpersona_acargo (ddjj_id)');
        $this->addSql('CREATE INDEX IDX_142D503C7BAB6B47 ON personal_ddjjpersona_acargo (personas_acargo_id)');
        $this->addSql('CREATE INDEX IDX_142D503CFE35D8C4 ON personal_ddjjpersona_acargo (creado_por_id)');
        $this->addSql('CREATE INDEX IDX_142D503CF6167A1C ON personal_ddjjpersona_acargo (actualizado_por_id)');
        $this->addSql('ALTER TABLE personal_ddjjpersona_acargo ADD CONSTRAINT FK_142D503C6FE99E6D FOREIGN KEY (ddjj_id) REFERENCES personal_declaracion_jurada (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_ddjjpersona_acargo ADD CONSTRAINT FK_142D503C7BAB6B47 FOREIGN KEY (personas_acargo_id) REFERENCES persona_a_cargo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_ddjjpersona_acargo ADD CONSTRAINT FK_142D503CFE35D8C4 FOREIGN KEY (creado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_ddjjpersona_acargo ADD CONSTRAINT FK_142D503CF6167A1C FOREIGN KEY (actualizado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_declaracion_jurada ADD lugar_trabajo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE personal_declaracion_jurada ADD CONSTRAINT FK_5DC8B8D995B11ABA FOREIGN KEY (lugar_trabajo_id) REFERENCES personal_lugar_trabajo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5DC8B8D995B11ABA ON personal_declaracion_jurada (lugar_trabajo_id)');
        $this->addSql('ALTER TABLE personal_lugar_trabajo DROP CONSTRAINT fk_ec1af7a1602bf2ce');
        $this->addSql('DROP INDEX idx_ec1af7a1602bf2ce');
        $this->addSql('ALTER TABLE personal_lugar_trabajo DROP legajo_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE personal_ddjjpersona_acargo_id_seq CASCADE');
        $this->addSql('DROP TABLE personal_ddjjpersona_acargo');
        $this->addSql('ALTER TABLE personal_declaracion_jurada DROP CONSTRAINT FK_5DC8B8D995B11ABA');
        $this->addSql('DROP INDEX IDX_5DC8B8D995B11ABA');
        $this->addSql('ALTER TABLE personal_declaracion_jurada DROP lugar_trabajo_id');
        $this->addSql('ALTER TABLE personal_lugar_trabajo ADD legajo_id INT NOT NULL');
        $this->addSql('ALTER TABLE personal_lugar_trabajo ADD CONSTRAINT fk_ec1af7a1602bf2ce FOREIGN KEY (legajo_id) REFERENCES personal_legajo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_ec1af7a1602bf2ce ON personal_lugar_trabajo (legajo_id)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200707185438 extends AbstractMigration
{
    public function getDescription() : string
    {
	    return 'RRHH v1';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE personal_licencia_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal_lugar_trabajo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal_declaracion_jurada_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal_asistencia_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal_novedad_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal_conyuge_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal_articulo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE personal_licencia (id INT NOT NULL, articulo_id INT DEFAULT NULL, creado_por_id INT DEFAULT NULL, actualizado_por_id INT DEFAULT NULL, fecha_desde DATE NOT NULL, fecha_hasta DATE NOT NULL, archivo VARCHAR(255) DEFAULT NULL, activo BOOLEAN NOT NULL, fecha_creacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fecha_actualizacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_90274C422DBC2FC9 ON personal_licencia (articulo_id)');
        $this->addSql('CREATE INDEX IDX_90274C42FE35D8C4 ON personal_licencia (creado_por_id)');
        $this->addSql('CREATE INDEX IDX_90274C42F6167A1C ON personal_licencia (actualizado_por_id)');
        $this->addSql('CREATE TABLE personal_lugar_trabajo (id INT NOT NULL, legajo_id INT NOT NULL, area_administrativa_id INT NOT NULL, creado_por_id INT DEFAULT NULL, actualizado_por_id INT DEFAULT NULL, fecha_desde DATE NOT NULL, fecha_hasta DATE DEFAULT NULL, actual BOOLEAN NOT NULL, activo BOOLEAN NOT NULL, fecha_creacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fecha_actualizacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EC1AF7A1602BF2CE ON personal_lugar_trabajo (legajo_id)');
        $this->addSql('CREATE INDEX IDX_EC1AF7A12EAC5EB1 ON personal_lugar_trabajo (area_administrativa_id)');
        $this->addSql('CREATE INDEX IDX_EC1AF7A1FE35D8C4 ON personal_lugar_trabajo (creado_por_id)');
        $this->addSql('CREATE INDEX IDX_EC1AF7A1F6167A1C ON personal_lugar_trabajo (actualizado_por_id)');
        $this->addSql('CREATE TABLE personal_declaracion_jurada (id INT NOT NULL, legajo_id INT NOT NULL, conyuge_id INT DEFAULT NULL, creado_por_id INT DEFAULT NULL, actualizado_por_id INT DEFAULT NULL, anio INT NOT NULL, tratamiento VARCHAR(255) DEFAULT NULL, situacion_revista VARCHAR(255) NOT NULL, profesion VARCHAR(255) DEFAULT NULL, nivel_estudios VARCHAR(255) DEFAULT NULL, titulo VARCHAR(255) DEFAULT NULL, anios_cursados INT DEFAULT NULL, fecha_presentacion DATE DEFAULT NULL, estado_civil VARCHAR(255) DEFAULT NULL, activo BOOLEAN NOT NULL, fecha_creacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fecha_actualizacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5DC8B8D9602BF2CE ON personal_declaracion_jurada (legajo_id)');
        $this->addSql('CREATE INDEX IDX_5DC8B8D9314923D ON personal_declaracion_jurada (conyuge_id)');
        $this->addSql('CREATE INDEX IDX_5DC8B8D9FE35D8C4 ON personal_declaracion_jurada (creado_por_id)');
        $this->addSql('CREATE INDEX IDX_5DC8B8D9F6167A1C ON personal_declaracion_jurada (actualizado_por_id)');
        $this->addSql('CREATE TABLE personal_asistencia (id INT NOT NULL, legajo_id INT NOT NULL, creado_por_id INT DEFAULT NULL, actualizado_por_id INT DEFAULT NULL, fecha DATE NOT NULL, hora_entrada TIME(0) WITHOUT TIME ZONE NOT NULL, hora_salida TIME(0) WITHOUT TIME ZONE DEFAULT NULL, tipo VARCHAR(255) NOT NULL, activo BOOLEAN NOT NULL, fecha_creacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fecha_actualizacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_98F6ED2D602BF2CE ON personal_asistencia (legajo_id)');
        $this->addSql('CREATE INDEX IDX_98F6ED2DFE35D8C4 ON personal_asistencia (creado_por_id)');
        $this->addSql('CREATE INDEX IDX_98F6ED2DF6167A1C ON personal_asistencia (actualizado_por_id)');
        $this->addSql('CREATE TABLE personal_novedad (id INT NOT NULL, legajo_id INT NOT NULL, creado_por_id INT DEFAULT NULL, actualizado_por_id INT DEFAULT NULL, observacion TEXT NOT NULL, fecha DATE NOT NULL, archivo VARCHAR(255) DEFAULT NULL, activo BOOLEAN NOT NULL, fecha_creacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fecha_actualizacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3FEDD267602BF2CE ON personal_novedad (legajo_id)');
        $this->addSql('CREATE INDEX IDX_3FEDD267FE35D8C4 ON personal_novedad (creado_por_id)');
        $this->addSql('CREATE INDEX IDX_3FEDD267F6167A1C ON personal_novedad (actualizado_por_id)');
        $this->addSql('CREATE TABLE personal_conyuge (id INT NOT NULL, persona_id INT DEFAULT NULL, creado_por_id INT DEFAULT NULL, actualizado_por_id INT DEFAULT NULL, estado VARCHAR(255) NOT NULL, fecha_enlace DATE DEFAULT NULL, lugar_enlace VARCHAR(255) DEFAULT NULL, lugar_trabajo VARCHAR(255) DEFAULT NULL, razon_social_lugar_trabajo VARCHAR(255) DEFAULT NULL, percibe_asignacion_familiar BOOLEAN NOT NULL, observaciones TEXT DEFAULT NULL, activo BOOLEAN NOT NULL, fecha_creacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fecha_actualizacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_28DCD994F5F88DB9 ON personal_conyuge (persona_id)');
        $this->addSql('CREATE INDEX IDX_28DCD994FE35D8C4 ON personal_conyuge (creado_por_id)');
        $this->addSql('CREATE INDEX IDX_28DCD994F6167A1C ON personal_conyuge (actualizado_por_id)');
        $this->addSql('CREATE TABLE personal_articulo (id INT NOT NULL, creado_por_id INT DEFAULT NULL, actualizado_por_id INT DEFAULT NULL, numero VARCHAR(255) NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, activo BOOLEAN NOT NULL, fecha_creacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fecha_actualizacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C5D690D8FE35D8C4 ON personal_articulo (creado_por_id)');
        $this->addSql('CREATE INDEX IDX_C5D690D8F6167A1C ON personal_articulo (actualizado_por_id)');
        $this->addSql('ALTER TABLE personal_licencia ADD CONSTRAINT FK_90274C422DBC2FC9 FOREIGN KEY (articulo_id) REFERENCES personal_articulo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_licencia ADD CONSTRAINT FK_90274C42FE35D8C4 FOREIGN KEY (creado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_licencia ADD CONSTRAINT FK_90274C42F6167A1C FOREIGN KEY (actualizado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_lugar_trabajo ADD CONSTRAINT FK_EC1AF7A1602BF2CE FOREIGN KEY (legajo_id) REFERENCES personal_legajo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_lugar_trabajo ADD CONSTRAINT FK_EC1AF7A12EAC5EB1 FOREIGN KEY (area_administrativa_id) REFERENCES area_administrativa (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_lugar_trabajo ADD CONSTRAINT FK_EC1AF7A1FE35D8C4 FOREIGN KEY (creado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_lugar_trabajo ADD CONSTRAINT FK_EC1AF7A1F6167A1C FOREIGN KEY (actualizado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_declaracion_jurada ADD CONSTRAINT FK_5DC8B8D9602BF2CE FOREIGN KEY (legajo_id) REFERENCES personal_legajo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_declaracion_jurada ADD CONSTRAINT FK_5DC8B8D9314923D FOREIGN KEY (conyuge_id) REFERENCES personal_conyuge (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_declaracion_jurada ADD CONSTRAINT FK_5DC8B8D9FE35D8C4 FOREIGN KEY (creado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_declaracion_jurada ADD CONSTRAINT FK_5DC8B8D9F6167A1C FOREIGN KEY (actualizado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_asistencia ADD CONSTRAINT FK_98F6ED2D602BF2CE FOREIGN KEY (legajo_id) REFERENCES personal_legajo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_asistencia ADD CONSTRAINT FK_98F6ED2DFE35D8C4 FOREIGN KEY (creado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_asistencia ADD CONSTRAINT FK_98F6ED2DF6167A1C FOREIGN KEY (actualizado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_novedad ADD CONSTRAINT FK_3FEDD267602BF2CE FOREIGN KEY (legajo_id) REFERENCES personal_legajo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_novedad ADD CONSTRAINT FK_3FEDD267FE35D8C4 FOREIGN KEY (creado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_novedad ADD CONSTRAINT FK_3FEDD267F6167A1C FOREIGN KEY (actualizado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_conyuge ADD CONSTRAINT FK_28DCD994F5F88DB9 FOREIGN KEY (persona_id) REFERENCES persona (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_conyuge ADD CONSTRAINT FK_28DCD994FE35D8C4 FOREIGN KEY (creado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_conyuge ADD CONSTRAINT FK_28DCD994F6167A1C FOREIGN KEY (actualizado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_articulo ADD CONSTRAINT FK_C5D690D8FE35D8C4 FOREIGN KEY (creado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_articulo ADD CONSTRAINT FK_C5D690D8F6167A1C FOREIGN KEY (actualizado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_legajo DROP situacion_revista');
        $this->addSql('ALTER TABLE personal_legajo DROP tratamiento');
        $this->addSql('ALTER TABLE personal_legajo DROP profesion');
        $this->addSql('ALTER TABLE persona ADD lugar_nacimiento VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ALTER roles DROP DEFAULT');
        $this->addSql('ALTER TABLE fos_user ALTER roles DROP NOT NULL');
        $this->addSql('ALTER TABLE persona_a_cargo ADD estudios_cursados VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE persona_a_cargo ADD convive_con_declarante BOOLEAN NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');


        $this->addSql('ALTER TABLE personal_declaracion_jurada DROP CONSTRAINT FK_5DC8B8D9314923D');
        $this->addSql('ALTER TABLE personal_licencia DROP CONSTRAINT FK_90274C422DBC2FC9');
        $this->addSql('DROP SEQUENCE personal_licencia_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal_lugar_trabajo_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal_declaracion_jurada_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal_asistencia_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal_novedad_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal_conyuge_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal_articulo_id_seq CASCADE');
        $this->addSql('DROP TABLE personal_licencia');
        $this->addSql('DROP TABLE personal_lugar_trabajo');
        $this->addSql('DROP TABLE personal_declaracion_jurada');
        $this->addSql('DROP TABLE personal_asistencia');
        $this->addSql('DROP TABLE personal_novedad');
        $this->addSql('DROP TABLE personal_conyuge');
        $this->addSql('DROP TABLE personal_articulo');
        $this->addSql('ALTER TABLE persona DROP lugar_nacimiento');
        $this->addSql('ALTER TABLE fos_user ALTER roles SET DEFAULT \'[]\'');
        $this->addSql('ALTER TABLE fos_user ALTER roles SET NOT NULL');
        $this->addSql('ALTER TABLE personal_legajo ADD situacion_revista VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE personal_legajo ADD tratamiento VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE personal_legajo ADD profesion VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE persona_a_cargo DROP estudios_cursados');
        $this->addSql('ALTER TABLE persona_a_cargo DROP convive_con_declarante');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200703234842 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE aprobado_en_sesion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE configuracion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE aprobado_en_sesion (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT DEFAULT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE configuracion (id INT NOT NULL, creado_por_id INT DEFAULT NULL, actualizado_por_id INT DEFAULT NULL, apple_touch_icon VARCHAR(255) DEFAULT NULL, encabezado_texto_definitivo VARCHAR(255) DEFAULT NULL, escudo VARCHAR(255) DEFAULT NULL, logo16 VARCHAR(255) DEFAULT NULL, logo32 VARCHAR(255) DEFAULT NULL, logo129 VARCHAR(255) DEFAULT NULL, logo269 VARCHAR(255) DEFAULT NULL, logotipo VARCHAR(255) DEFAULT NULL, sello_presidencia VARCHAR(255) DEFAULT NULL, activo BOOLEAN NOT NULL, fecha_creacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fecha_actualizacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_682CCAF1FE35D8C4 ON configuracion (creado_por_id)');
        $this->addSql('CREATE INDEX IDX_682CCAF1F6167A1C ON configuracion (actualizado_por_id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE configuracion ADD CONSTRAINT FK_682CCAF1FE35D8C4 FOREIGN KEY (creado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE configuracion ADD CONSTRAINT FK_682CCAF1F6167A1C FOREIGN KEY (actualizado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX uniq_957a6479a0d96fbf');
        $this->addSql('DROP INDEX uniq_957a6479c05fb297');
        $this->addSql('DROP INDEX uniq_957a647992fc23a8');
        $this->addSql('ALTER TABLE fos_user DROP username_canonical');
        $this->addSql('ALTER TABLE fos_user DROP email_canonical');
        $this->addSql('ALTER TABLE fos_user DROP salt');
        $this->addSql('ALTER TABLE fos_user DROP last_login');
        $this->addSql('ALTER TABLE fos_user DROP confirmation_token');
        $this->addSql('ALTER TABLE fos_user DROP password_requested_at');
        $this->addSql('ALTER TABLE fos_user ALTER username TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE fos_user ALTER enabled DROP NOT NULL');
	    $this->addSql('ALTER TABLE fos_user DROP roles');
	    $this->addSql('ALTER TABLE fos_user ALTER username TYPE VARCHAR(255)');
	    $this->addSql('ALTER TABLE fos_user ALTER enabled DROP NOT NULL');
	    $this->addSql("ALTER TABLE fos_user ADD roles JSON NOT NULL DEFAULT '[]'");
        $this->addSql('COMMENT ON COLUMN fos_user.roles IS NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479E7927C74 ON fos_user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479F85E0677 ON fos_user (username)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE aprobado_en_sesion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE configuracion_id_seq CASCADE');
        $this->addSql('DROP TABLE aprobado_en_sesion');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE configuracion');
        $this->addSql('DROP INDEX UNIQ_957A6479E7927C74');
        $this->addSql('DROP INDEX UNIQ_957A6479F85E0677');
        $this->addSql('ALTER TABLE fos_user ADD username_canonical VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE fos_user ADD email_canonical VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE fos_user ADD salt VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD confirmation_token VARCHAR(180) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ALTER roles TYPE TEXT');
        $this->addSql('ALTER TABLE fos_user ALTER roles DROP DEFAULT');
        $this->addSql('ALTER TABLE fos_user ALTER roles SET NOT NULL');
        $this->addSql('ALTER TABLE fos_user ALTER username TYPE VARCHAR(180)');
        $this->addSql('ALTER TABLE fos_user ALTER enabled SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN fos_user.roles IS \'(DC2Type:array)\'');
        $this->addSql('CREATE UNIQUE INDEX uniq_957a6479a0d96fbf ON fos_user (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX uniq_957a6479c05fb297 ON fos_user (confirmation_token)');
        $this->addSql('CREATE UNIQUE INDEX uniq_957a647992fc23a8 ON fos_user (username_canonical)');
    }
}

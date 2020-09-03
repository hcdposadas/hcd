<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200708225323 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE personal_ddjjconyuge_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE personal_ddjjconyuge (id INT NOT NULL, ddjj_id INT NOT NULL, conyuge_id INT NOT NULL, creado_por_id INT DEFAULT NULL, actualizado_por_id INT DEFAULT NULL, activo BOOLEAN NOT NULL, fecha_creacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fecha_actualizacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3BE13B9B6FE99E6D ON personal_ddjjconyuge (ddjj_id)');
        $this->addSql('CREATE INDEX IDX_3BE13B9B314923D ON personal_ddjjconyuge (conyuge_id)');
        $this->addSql('CREATE INDEX IDX_3BE13B9BFE35D8C4 ON personal_ddjjconyuge (creado_por_id)');
        $this->addSql('CREATE INDEX IDX_3BE13B9BF6167A1C ON personal_ddjjconyuge (actualizado_por_id)');
        $this->addSql('ALTER TABLE personal_ddjjconyuge ADD CONSTRAINT FK_3BE13B9B6FE99E6D FOREIGN KEY (ddjj_id) REFERENCES personal_declaracion_jurada (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_ddjjconyuge ADD CONSTRAINT FK_3BE13B9B314923D FOREIGN KEY (conyuge_id) REFERENCES personal_conyuge (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_ddjjconyuge ADD CONSTRAINT FK_3BE13B9BFE35D8C4 FOREIGN KEY (creado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_ddjjconyuge ADD CONSTRAINT FK_3BE13B9BF6167A1C FOREIGN KEY (actualizado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personal_declaracion_jurada DROP CONSTRAINT fk_5dc8b8d9314923d');
        $this->addSql('DROP INDEX idx_5dc8b8d9314923d');
        $this->addSql('ALTER TABLE personal_declaracion_jurada DROP conyuge_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE personal_ddjjconyuge_id_seq CASCADE');
        $this->addSql('DROP TABLE personal_ddjjconyuge');
        $this->addSql('ALTER TABLE personal_declaracion_jurada ADD conyuge_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE personal_declaracion_jurada ADD CONSTRAINT fk_5dc8b8d9314923d FOREIGN KEY (conyuge_id) REFERENCES personal_conyuge (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5dc8b8d9314923d ON personal_declaracion_jurada (conyuge_id)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200907151135 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE firmante_texto_definitivo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE firmante_texto_definitivo (id INT NOT NULL, iniciador_id INT DEFAULT NULL, texto_definitivo_id INT DEFAULT NULL, creado_por_id INT DEFAULT NULL, actualizado_por_id INT DEFAULT NULL, presidente BOOLEAN DEFAULT \'false\' NOT NULL, activo BOOLEAN NOT NULL, fecha_creacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fecha_actualizacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8240EB4DB6C49A37 ON firmante_texto_definitivo (iniciador_id)');
        $this->addSql('CREATE INDEX IDX_8240EB4DAD63366F ON firmante_texto_definitivo (texto_definitivo_id)');
        $this->addSql('CREATE INDEX IDX_8240EB4DFE35D8C4 ON firmante_texto_definitivo (creado_por_id)');
        $this->addSql('CREATE INDEX IDX_8240EB4DF6167A1C ON firmante_texto_definitivo (actualizado_por_id)');
        $this->addSql('ALTER TABLE firmante_texto_definitivo ADD CONSTRAINT FK_8240EB4DB6C49A37 FOREIGN KEY (iniciador_id) REFERENCES iniciador (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE firmante_texto_definitivo ADD CONSTRAINT FK_8240EB4DAD63366F FOREIGN KEY (texto_definitivo_id) REFERENCES texto_definitivo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE firmante_texto_definitivo ADD CONSTRAINT FK_8240EB4DFE35D8C4 FOREIGN KEY (creado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE firmante_texto_definitivo ADD CONSTRAINT FK_8240EB4DF6167A1C FOREIGN KEY (actualizado_por_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE texto_definitivo ADD tipo_texto_definitivo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE texto_definitivo ADD CONSTRAINT FK_E313F079EB930B87 FOREIGN KEY (tipo_texto_definitivo_id) REFERENCES tipo_proyecto (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E313F079EB930B87 ON texto_definitivo (tipo_texto_definitivo_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE firmante_texto_definitivo_id_seq CASCADE');
        $this->addSql('DROP TABLE firmante_texto_definitivo');
        $this->addSql('ALTER TABLE texto_definitivo DROP CONSTRAINT FK_E313F079EB930B87');
        $this->addSql('DROP INDEX IDX_E313F079EB930B87');
        $this->addSql('ALTER TABLE texto_definitivo DROP tipo_texto_definitivo_id');
    }
}

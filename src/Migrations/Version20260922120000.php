<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Cambio de cabecera: marca en proyecto_b_a_e, la comisión que era cabecera
 * antes y el giro que el cambio agregó (para poder corregirlo).
 */
final class Version20260922120000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Agrega a proyecto_b_a_e los campos del cambio de cabecera';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE proyecto_b_a_e ADD es_cambio_cabecera BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE proyecto_b_a_e ADD comision_cabecera_anterior_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE proyecto_b_a_e ADD giro_cabecera_agregado_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE proyecto_b_a_e ADD CONSTRAINT FK_FDBBB9D142963869 FOREIGN KEY (comision_cabecera_anterior_id) REFERENCES comision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE proyecto_b_a_e ADD CONSTRAINT FK_FDBBB9D17073188C FOREIGN KEY (giro_cabecera_agregado_id) REFERENCES giro (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FDBBB9D142963869 ON proyecto_b_a_e (comision_cabecera_anterior_id)');
        $this->addSql('CREATE INDEX IDX_FDBBB9D17073188C ON proyecto_b_a_e (giro_cabecera_agregado_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE proyecto_b_a_e DROP CONSTRAINT FK_FDBBB9D142963869');
        $this->addSql('ALTER TABLE proyecto_b_a_e DROP CONSTRAINT FK_FDBBB9D17073188C');
        $this->addSql('DROP INDEX IDX_FDBBB9D142963869');
        $this->addSql('DROP INDEX IDX_FDBBB9D17073188C');
        $this->addSql('ALTER TABLE proyecto_b_a_e DROP es_cambio_cabecera');
        $this->addSql('ALTER TABLE proyecto_b_a_e DROP comision_cabecera_anterior_id');
        $this->addSql('ALTER TABLE proyecto_b_a_e DROP giro_cabecera_agregado_id');
    }
}

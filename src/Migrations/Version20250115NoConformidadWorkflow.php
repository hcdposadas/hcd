<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250115NoConformidadWorkflow extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Agregar campos de workflow a la tabla no_conformidad';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE no_conformidad ADD asignado_a_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE no_conformidad ADD correccion TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE no_conformidad ADD fecha_correccion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE no_conformidad ADD analisis_causa TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE no_conformidad ADD accion_correctiva TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE no_conformidad ADD fecha_accion_correctiva TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE no_conformidad ADD correccion_verificada BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE no_conformidad ADD efectividad_verificada BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE no_conformidad ADD comentarios_verificacion TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE no_conformidad ADD CONSTRAINT FK_NC_ASIGNADO_A FOREIGN KEY (asignado_a_id) REFERENCES usuario (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_NC_ASIGNADO_A ON no_conformidad (asignado_a_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE no_conformidad DROP CONSTRAINT FK_NC_ASIGNADO_A');
        $this->addSql('DROP INDEX IDX_NC_ASIGNADO_A');
        $this->addSql('ALTER TABLE no_conformidad DROP asignado_a_id');
        $this->addSql('ALTER TABLE no_conformidad DROP correccion');
        $this->addSql('ALTER TABLE no_conformidad DROP fecha_correccion');
        $this->addSql('ALTER TABLE no_conformidad DROP analisis_causa');
        $this->addSql('ALTER TABLE no_conformidad DROP accion_correctiva');
        $this->addSql('ALTER TABLE no_conformidad DROP fecha_accion_correctiva');
        $this->addSql('ALTER TABLE no_conformidad DROP correccion_verificada');
        $this->addSql('ALTER TABLE no_conformidad DROP efectividad_verificada');
        $this->addSql('ALTER TABLE no_conformidad DROP comentarios_verificacion');
    }
} 
<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration para agregar tickets relacionados y archivos adjuntos
 */
final class Version20240528TicketRelacionados extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Agrega soporte para tickets relacionados y archivos adjuntos';
    }

    public function up(Schema $schema): void
    {
        // Agregar campos para los archivos adjuntos
        $this->addSql('ALTER TABLE ticket ADD adjunto_nombre VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket ADD updated_at DATETIME DEFAULT NULL');
        
        // Agregar relación para tickets relacionados
        $this->addSql('ALTER TABLE ticket ADD ticket_padre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3EF08A5 FOREIGN KEY (ticket_padre_id) REFERENCES ticket (id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA3EF08A5 ON ticket (ticket_padre_id)');
    }

    public function down(Schema $schema): void
    {
        // Eliminar relación para tickets relacionados
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3EF08A5');
        $this->addSql('DROP INDEX IDX_97A0ADA3EF08A5 ON ticket');
        $this->addSql('ALTER TABLE ticket DROP ticket_padre_id');
        
        // Eliminar campos para los archivos adjuntos
        $this->addSql('ALTER TABLE ticket DROP adjunto_nombre');
        $this->addSql('ALTER TABLE ticket DROP updated_at');
    }
} 
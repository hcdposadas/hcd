<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250116AddCreadoPorToNoConformidad extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Agregar campo creado_por_id a la tabla no_conformidad';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE no_conformidad ADD creado_por_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE no_conformidad ADD CONSTRAINT FK_NC_CREADO_POR FOREIGN KEY (creado_por_id) REFERENCES usuario (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_NC_CREADO_POR ON no_conformidad (creado_por_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE no_conformidad DROP CONSTRAINT FK_NC_CREADO_POR');
        $this->addSql('DROP INDEX IDX_NC_CREADO_POR');
        $this->addSql('ALTER TABLE no_conformidad DROP creado_por_id');
    }
} 
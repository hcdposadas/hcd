<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240410000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crear tabla no_conformidad';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE no_conformidad (id SERIAL NOT NULL, area_id INT NOT NULL, persona VARCHAR(255) NOT NULL, origen VARCHAR(50) NOT NULL, categoria VARCHAR(50) NOT NULL, requisito TEXT NOT NULL, fecha TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, imagen VARCHAR(255) DEFAULT NULL, documento VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_NO_CONFORMIDAD_AREA ON no_conformidad (area_id)');
        $this->addSql('ALTER TABLE no_conformidad ADD CONSTRAINT FK_NO_CONFORMIDAD_AREA FOREIGN KEY (area_id) REFERENCES area_administrativa (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE no_conformidad');
    }
} 
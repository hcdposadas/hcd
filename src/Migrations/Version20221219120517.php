<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221219120517 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE persona DROP CONSTRAINT fk_51e5b69b7310dad4');
        $this->addSql('DROP INDEX uniq_51e5b69b7310dad4');
        $this->addSql('ALTER TABLE persona DROP paciente_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE persona ADD paciente_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE persona ADD CONSTRAINT fk_51e5b69b7310dad4 FOREIGN KEY (paciente_id) REFERENCES paciente (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_51e5b69b7310dad4 ON persona (paciente_id)');
    }
}

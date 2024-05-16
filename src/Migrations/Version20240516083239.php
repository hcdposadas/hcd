<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240516083239 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE proyecto_b_a_e ADD digesto VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE texto_definitivo ADD archivo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dictamen ALTER aprobado_legislativo TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE dictamen ALTER aprobado_legislativo DROP DEFAULT');
        $this->addSql('ALTER TABLE giro DROP anexo_legislativo');
        $this->addSql('ALTER TABLE giro DROP anexo_secretaria');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE proyecto_b_a_e DROP digesto');
        $this->addSql('ALTER TABLE texto_definitivo DROP archivo');
        $this->addSql('ALTER TABLE dictamen ALTER aprobado_legislativo TYPE BOOLEAN');
        $this->addSql('ALTER TABLE dictamen ALTER aprobado_legislativo DROP DEFAULT');
        $this->addSql('ALTER TABLE giro ADD anexo_legislativo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE giro ADD anexo_secretaria VARCHAR(255) DEFAULT NULL');
    }
}

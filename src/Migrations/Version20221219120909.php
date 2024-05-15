<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221219120909 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs


        $this->addSql('ALTER TABLE expediente ADD marca_definitivo BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE expediente ADD hash VARCHAR(255) DEFAULT NULL');
	$this->addSql('ALTER TABLE expediente ADD marca_temporal TEXT DEFAULT NULL');
        

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');


        $this->addSql('ALTER TABLE expediente DROP marca_definitivo ');
        $this->addSql('ALTER TABLE expediente DROP hash ');
	$this->addSql('ALTER TABLE expediente DROP marca_temporal ');
        

    }
}

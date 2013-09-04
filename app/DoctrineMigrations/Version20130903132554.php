<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130903132554 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE authentication_method CHANGE email_token email_token VARCHAR(40) DEFAULT NULL, CHANGE registration_code registration_code VARCHAR(8) DEFAULT NULL, CHANGE approved_at approved_at DATETIME DEFAULT NULL, CHANGE requested_at requested_at DATETIME DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE authentication_method CHANGE email_token email_token VARCHAR(40) NOT NULL, CHANGE registration_code registration_code VARCHAR(8) NOT NULL, CHANGE approved_at approved_at DATETIME NOT NULL, CHANGE requested_at requested_at DATETIME NOT NULL");
    }
}

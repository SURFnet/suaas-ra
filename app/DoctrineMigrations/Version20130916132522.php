<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130916132522 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE mollie_otp DROP FOREIGN KEY FK_3A81C22E9FFB9C05");
        $this->addSql("ALTER TABLE mollie_otp ADD CONSTRAINT FK_3A81C22E9FFB9C05 FOREIGN KEY (mollie_token_id) REFERENCES authentication_method (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE mollie_otp DROP FOREIGN KEY FK_3A81C22E9FFB9C05");
        $this->addSql("ALTER TABLE mollie_otp ADD CONSTRAINT FK_3A81C22E9FFB9C05 FOREIGN KEY (mollie_token_id) REFERENCES authentication_method (id)");
    }
}

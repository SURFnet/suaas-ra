<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130916102802 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE mollie_otp (id INT AUTO_INCREMENT NOT NULL, mollie_token_id INT NOT NULL, otp VARCHAR(60) NOT NULL, requested_at DATETIME NOT NULL, confirmed_at DATETIME DEFAULT NULL, INDEX IDX_3A81C22E9FFB9C05 (mollie_token_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE mollie_otp ADD CONSTRAINT FK_3A81C22E9FFB9C05 FOREIGN KEY (mollie_token_id) REFERENCES authentication_method (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE mollie_otp");
    }
}

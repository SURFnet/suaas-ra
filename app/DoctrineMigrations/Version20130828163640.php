<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130828163640 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE authentication_method (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, approved_by INT DEFAULT NULL, email_token VARCHAR(40) NOT NULL, registration_code VARCHAR(8) NOT NULL, approved_at DATETIME NOT NULL, revoked_at DATETIME DEFAULT NULL, requested_at DATETIME NOT NULL, last_used_at DATETIME NOT NULL, type VARCHAR(255) NOT NULL, phone_number VARCHAR(24) DEFAULT NULL, INDEX IDX_17DA06C67E3C61F9 (owner_id), INDEX IDX_17DA06C64EA3CB3D (approved_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE organisation (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE registration_authority (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, contact_info VARCHAR(200) DEFAULT NULL, location VARCHAR(200) DEFAULT NULL, UNIQUE INDEX UNIQ_A9A59499A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, organisation INT DEFAULT NULL, name_id VARCHAR(64) NOT NULL, display_name VARCHAR(150) NOT NULL, email VARCHAR(360) NOT NULL, INDEX IDX_8D93D649E6E132B4 (organisation), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE authentication_method ADD CONSTRAINT FK_17DA06C67E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)");
        $this->addSql("ALTER TABLE authentication_method ADD CONSTRAINT FK_17DA06C64EA3CB3D FOREIGN KEY (approved_by) REFERENCES user (id)");
        $this->addSql("ALTER TABLE registration_authority ADD CONSTRAINT FK_A9A59499A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)");
        $this->addSql("ALTER TABLE user ADD CONSTRAINT FK_8D93D649E6E132B4 FOREIGN KEY (organisation) REFERENCES organisation (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE user DROP FOREIGN KEY FK_8D93D649E6E132B4");
        $this->addSql("ALTER TABLE authentication_method DROP FOREIGN KEY FK_17DA06C67E3C61F9");
        $this->addSql("ALTER TABLE authentication_method DROP FOREIGN KEY FK_17DA06C64EA3CB3D");
        $this->addSql("ALTER TABLE registration_authority DROP FOREIGN KEY FK_A9A59499A76ED395");
        $this->addSql("DROP TABLE authentication_method");
        $this->addSql("DROP TABLE organisation");
        $this->addSql("DROP TABLE registration_authority");
        $this->addSql("DROP TABLE user");
    }
}

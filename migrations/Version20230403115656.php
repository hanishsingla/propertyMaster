<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230403115656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agent (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', agent_number VARCHAR(255) DEFAULT NULL, agent_name VARCHAR(255) NOT NULL, is_deleted TINYINT(1) DEFAULT 0 NOT NULL, is_created_at DATETIME NOT NULL, is_updated_at DATETIME DEFAULT NULL, is_deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', is_deleted TINYINT(1) DEFAULT 0 NOT NULL, is_created_at DATETIME NOT NULL, is_updated_at DATETIME DEFAULT NULL, is_deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE helpdesk (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', username VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, message VARCHAR(255) NOT NULL, is_deleted TINYINT(1) DEFAULT 0 NOT NULL, is_created_at DATETIME NOT NULL, is_updated_at DATETIME DEFAULT NULL, is_deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1D6CD3B1E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', property_is_garage TINYINT(1) DEFAULT 0 NOT NULL, property_area VARCHAR(255) NOT NULL, property_category VARCHAR(255) NOT NULL, property_city VARCHAR(255) NOT NULL, property_country VARCHAR(255) NOT NULL, property_description VARCHAR(255) NOT NULL, property_garage VARCHAR(255) DEFAULT NULL, property_image JSON DEFAULT NULL, property_price VARCHAR(255) NOT NULL, property_rooms VARCHAR(255) NOT NULL, property_state VARCHAR(255) NOT NULL, property_title VARCHAR(255) NOT NULL, property_type VARCHAR(255) NOT NULL, room_bed VARCHAR(255) NOT NULL, short_description VARCHAR(255) NOT NULL, square_type VARCHAR(255) NOT NULL, is_deleted TINYINT(1) DEFAULT 0 NOT NULL, is_created_at DATETIME NOT NULL, is_updated_at DATETIME DEFAULT NULL, is_deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B9983CE5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, is_deleted TINYINT(1) DEFAULT 0 NOT NULL, is_created_at DATETIME NOT NULL, is_updated_at DATETIME DEFAULT NULL, is_deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_52825A88E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_user_address (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, gender VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, mobile VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, address2 VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, zip VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, is_deleted TINYINT(1) DEFAULT 0 NOT NULL, is_created_at DATETIME NOT NULL, is_updated_at DATETIME DEFAULT NULL, is_deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_6FD68F12A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rememberme_token (series VARCHAR(88) NOT NULL, value VARCHAR(88) NOT NULL, lastUsed DATETIME NOT NULL, class VARCHAR(100) NOT NULL, username VARCHAR(200) NOT NULL, PRIMARY KEY(series)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reset_password ADD CONSTRAINT FK_B9983CE5A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id)');
        $this->addSql('ALTER TABLE security_user_address ADD CONSTRAINT FK_6FD68F12A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reset_password DROP FOREIGN KEY FK_B9983CE5A76ED395');
        $this->addSql('ALTER TABLE security_user_address DROP FOREIGN KEY FK_6FD68F12A76ED395');
        $this->addSql('DROP TABLE agent');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE helpdesk');
        $this->addSql('DROP TABLE property');
        $this->addSql('DROP TABLE reset_password');
        $this->addSql('DROP TABLE security_user');
        $this->addSql('DROP TABLE security_user_address');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE rememberme_token');
    }
}

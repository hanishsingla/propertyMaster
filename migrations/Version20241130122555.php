<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241130122555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', owner_id VARCHAR(255) NOT NULL, is_deleted TINYINT(1) DEFAULT 0 NOT NULL, is_created_at DATETIME NOT NULL, is_updated_at DATETIME DEFAULT NULL, is_deleted_at DATETIME DEFAULT NULL, INDEX index_id (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favourite_properties (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', property_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', owner_id VARCHAR(255) NOT NULL, favourite VARCHAR(255) NOT NULL, is_deleted TINYINT(1) DEFAULT 0 NOT NULL, is_created_at DATETIME NOT NULL, is_updated_at DATETIME DEFAULT NULL, is_deleted_at DATETIME DEFAULT NULL, INDEX IDX_632A835549213EC (property_id), INDEX index_id (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE helpdesk_tickets (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', owner_id VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, message VARCHAR(255) NOT NULL, is_deleted TINYINT(1) DEFAULT 0 NOT NULL, is_created_at DATETIME NOT NULL, is_updated_at DATETIME DEFAULT NULL, is_deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_473F2F7AE7927C74 (email), INDEX index_id (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE properties (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', owner_id VARCHAR(255) NOT NULL, property_is_garage TINYINT(1) DEFAULT 0 NOT NULL, property_area VARCHAR(255) NOT NULL, property_bath_rooms VARCHAR(255) NOT NULL, property_category VARCHAR(255) NOT NULL, property_city VARCHAR(255) NOT NULL, property_country VARCHAR(255) NOT NULL, property_description VARCHAR(1000) NOT NULL, property_direction VARCHAR(255) NOT NULL, property_garage VARCHAR(255) DEFAULT NULL, property_image JSON DEFAULT NULL, property_price VARCHAR(255) NOT NULL, property_rooms VARCHAR(255) DEFAULT NULL, property_state VARCHAR(255) NOT NULL, property_status VARCHAR(255) NOT NULL, property_title VARCHAR(255) NOT NULL, property_type VARCHAR(255) NOT NULL, property_bed_rooms VARCHAR(255) DEFAULT NULL, square_type VARCHAR(255) NOT NULL, is_deleted TINYINT(1) DEFAULT 0 NOT NULL, is_created_at DATETIME NOT NULL, is_updated_at DATETIME DEFAULT NULL, is_deleted_at DATETIME DEFAULT NULL, INDEX index_id (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B9983CE5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_user_details (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, mobile VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, address2 VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, zip VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, is_deleted TINYINT(1) DEFAULT 0 NOT NULL, is_created_at DATETIME NOT NULL, is_updated_at DATETIME DEFAULT NULL, is_deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_10BEEB19A76ED395 (user_id), INDEX index_id (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE security_users (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', is_agent TINYINT(1) DEFAULT 0 NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, is_deleted TINYINT(1) DEFAULT 0 NOT NULL, is_created_at DATETIME NOT NULL, is_updated_at DATETIME DEFAULT NULL, is_deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_F83F4643E7927C74 (email), INDEX index_id (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rememberme_token (series VARCHAR(88) NOT NULL, value VARCHAR(88) NOT NULL, lastUsed DATETIME NOT NULL, class VARCHAR(100) NOT NULL, username VARCHAR(200) NOT NULL, PRIMARY KEY(series)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favourite_properties ADD CONSTRAINT FK_632A835549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
        $this->addSql('ALTER TABLE reset_password ADD CONSTRAINT FK_B9983CE5A76ED395 FOREIGN KEY (user_id) REFERENCES security_users (id)');
        $this->addSql('ALTER TABLE security_user_details ADD CONSTRAINT FK_10BEEB19A76ED395 FOREIGN KEY (user_id) REFERENCES security_users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favourite_properties DROP FOREIGN KEY FK_632A835549213EC');
        $this->addSql('ALTER TABLE reset_password DROP FOREIGN KEY FK_B9983CE5A76ED395');
        $this->addSql('ALTER TABLE security_user_details DROP FOREIGN KEY FK_10BEEB19A76ED395');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE favourite_properties');
        $this->addSql('DROP TABLE helpdesk_tickets');
        $this->addSql('DROP TABLE properties');
        $this->addSql('DROP TABLE reset_password');
        $this->addSql('DROP TABLE security_user_details');
        $this->addSql('DROP TABLE security_users');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE rememberme_token');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260702034411 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favourite_properties (created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, id CHAR(36) NOT NULL, user_id CHAR(36) NOT NULL, property_id CHAR(36) NOT NULL, INDEX IDX_632A835A76ED395 (user_id), INDEX IDX_632A835549213EC (property_id), INDEX index_id (id), UNIQUE INDEX uniq_user_property (user_id, property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE helpdesk_tickets (created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, message LONGTEXT NOT NULL, user_id CHAR(36) DEFAULT NULL, INDEX IDX_473F2F7AA76ED395 (user_id), INDEX index_id (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE properties (created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, id CHAR(36) NOT NULL, title VARCHAR(180) NOT NULL, slug VARCHAR(200) NOT NULL, description LONGTEXT NOT NULL, listing_type VARCHAR(20) NOT NULL, category VARCHAR(40) NOT NULL, type VARCHAR(20) NOT NULL, status VARCHAR(20) DEFAULT \'draft\' NOT NULL, price_minor BIGINT NOT NULL, currency VARCHAR(3) DEFAULT \'INR\' NOT NULL, area INT NOT NULL, area_unit VARCHAR(10) DEFAULT \'sq_ft\' NOT NULL, bed_rooms INT DEFAULT NULL, bath_rooms INT DEFAULT NULL, rooms INT DEFAULT NULL, direction VARCHAR(10) DEFAULT NULL, city VARCHAR(255) NOT NULL, state VARCHAR(255) DEFAULT \'Punjab\' NOT NULL, country VARCHAR(255) DEFAULT \'India\' NOT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, is_featured TINYINT(1) DEFAULT 0 NOT NULL, owner_id CHAR(36) NOT NULL, UNIQUE INDEX UNIQ_87C331C7989D9B62 (slug), INDEX IDX_87C331C77E3C61F9 (owner_id), INDEX index_id (id), INDEX index_slug (slug), INDEX index_status (status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE property_images (created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, id CHAR(36) NOT NULL, path VARCHAR(255) NOT NULL, sort_order INT DEFAULT 0 NOT NULL, is_cover TINYINT(1) DEFAULT 0 NOT NULL, property_id CHAR(36) NOT NULL, INDEX IDX_9E68D116549213EC (property_id), INDEX index_id (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reset_password (id CHAR(36) NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, user_id CHAR(36) NOT NULL, INDEX IDX_B9983CE5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE security_users (created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, id CHAR(36) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_agent TINYINT(1) DEFAULT 0 NOT NULL, is_verified TINYINT(1) NOT NULL, name VARCHAR(255) DEFAULT NULL, gender VARCHAR(20) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, mobile VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, address2 VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, zip VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_F83F4643E7927C74 (email), INDEX index_id (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rememberme_token (series VARCHAR(88) NOT NULL, value VARCHAR(88) NOT NULL, lastUsed DATETIME NOT NULL, class VARCHAR(100) NOT NULL, username VARCHAR(200) NOT NULL, PRIMARY KEY(series)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE favourite_properties ADD CONSTRAINT FK_632A835A76ED395 FOREIGN KEY (user_id) REFERENCES security_users (id)');
        $this->addSql('ALTER TABLE favourite_properties ADD CONSTRAINT FK_632A835549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
        $this->addSql('ALTER TABLE helpdesk_tickets ADD CONSTRAINT FK_473F2F7AA76ED395 FOREIGN KEY (user_id) REFERENCES security_users (id)');
        $this->addSql('ALTER TABLE properties ADD CONSTRAINT FK_87C331C77E3C61F9 FOREIGN KEY (owner_id) REFERENCES security_users (id)');
        $this->addSql('ALTER TABLE property_images ADD CONSTRAINT FK_9E68D116549213EC FOREIGN KEY (property_id) REFERENCES properties (id)');
        $this->addSql('ALTER TABLE reset_password ADD CONSTRAINT FK_B9983CE5A76ED395 FOREIGN KEY (user_id) REFERENCES security_users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favourite_properties DROP FOREIGN KEY FK_632A835A76ED395');
        $this->addSql('ALTER TABLE favourite_properties DROP FOREIGN KEY FK_632A835549213EC');
        $this->addSql('ALTER TABLE helpdesk_tickets DROP FOREIGN KEY FK_473F2F7AA76ED395');
        $this->addSql('ALTER TABLE properties DROP FOREIGN KEY FK_87C331C77E3C61F9');
        $this->addSql('ALTER TABLE property_images DROP FOREIGN KEY FK_9E68D116549213EC');
        $this->addSql('ALTER TABLE reset_password DROP FOREIGN KEY FK_B9983CE5A76ED395');
        $this->addSql('DROP TABLE favourite_properties');
        $this->addSql('DROP TABLE helpdesk_tickets');
        $this->addSql('DROP TABLE properties');
        $this->addSql('DROP TABLE property_images');
        $this->addSql('DROP TABLE reset_password');
        $this->addSql('DROP TABLE security_users');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE rememberme_token');
    }
}

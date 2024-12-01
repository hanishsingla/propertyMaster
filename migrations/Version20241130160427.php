<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241130160427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories CHANGE id id CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE favourite_properties CHANGE id id CHAR(36) NOT NULL, CHANGE property_id property_id CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE helpdesk_tickets CHANGE id id CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE properties CHANGE id id CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE reset_password CHANGE id id CHAR(36) NOT NULL, CHANGE user_id user_id CHAR(36) NOT NULL, CHANGE requested_at requested_at DATETIME NOT NULL, CHANGE expires_at expires_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE security_user_details CHANGE id id CHAR(36) NOT NULL, CHANGE user_id user_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE security_users CHANGE id id CHAR(36) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE favourite_properties CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE property_id property_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE helpdesk_tickets CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE security_user_details CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE security_users CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE reset_password CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE requested_at requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE expires_at expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE user_id user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE properties CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230316070936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agent ADD is_deleted TINYINT(1) DEFAULT 0 NOT NULL, ADD is_created_at DATETIME NOT NULL, ADD is_updated_at DATETIME DEFAULT NULL, ADD is_deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD is_deleted TINYINT(1) DEFAULT 0 NOT NULL, ADD is_created_at DATETIME NOT NULL, ADD is_updated_at DATETIME DEFAULT NULL, ADD is_deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE property ADD property_price VARCHAR(255) NOT NULL, ADD property_area VARCHAR(255) NOT NULL, ADD property_address VARCHAR(255) NOT NULL, ADD short_description VARCHAR(255) NOT NULL, ADD property_description VARCHAR(255) NOT NULL, ADD is_deleted TINYINT(1) DEFAULT 0 NOT NULL, ADD is_created_at DATETIME NOT NULL, ADD is_updated_at DATETIME DEFAULT NULL, ADD is_deleted_at DATETIME DEFAULT NULL, CHANGE property_name property_title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD is_deleted TINYINT(1) DEFAULT 0 NOT NULL, ADD is_created_at DATETIME NOT NULL, ADD is_updated_at DATETIME DEFAULT NULL, ADD is_deleted_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agent DROP is_deleted, DROP is_created_at, DROP is_updated_at, DROP is_deleted_at');
        $this->addSql('ALTER TABLE category DROP is_deleted, DROP is_created_at, DROP is_updated_at, DROP is_deleted_at');
        $this->addSql('ALTER TABLE property ADD property_name VARCHAR(255) NOT NULL, DROP property_title, DROP property_price, DROP property_area, DROP property_address, DROP short_description, DROP property_description, DROP is_deleted, DROP is_created_at, DROP is_updated_at, DROP is_deleted_at');
        $this->addSql('ALTER TABLE `user` DROP is_deleted, DROP is_created_at, DROP is_updated_at, DROP is_deleted_at');
    }
}

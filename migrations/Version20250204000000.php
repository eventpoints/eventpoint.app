<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250204000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new fields to event table: updated_at, timezone, venue_name, max_attendees, status';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD timezone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD venue_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD max_attendees INT DEFAULT NULL');
        $this->addSql("ALTER TABLE event ADD status VARCHAR(30) NOT NULL DEFAULT 'draft'");
        $this->addSql('COMMENT ON COLUMN event.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event DROP updated_at');
        $this->addSql('ALTER TABLE event DROP timezone');
        $this->addSql('ALTER TABLE event DROP venue_name');
        $this->addSql('ALTER TABLE event DROP max_attendees');
        $this->addSql('ALTER TABLE event DROP status');
    }
}

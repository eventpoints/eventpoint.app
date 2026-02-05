<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Event Invitation System Refactor Migration
 *
 * This migration consolidates EventEmailInvitation, EventRequest, and EventRejection
 * into a unified EventInvitation entity with type and status enums.
 */
final class Version20250204120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Refactor event invitation system - consolidate entities into unified EventInvitation';
    }

    public function up(Schema $schema): void
    {
        // Step 1: Add new columns to event_invitation table
        $this->addSql('ALTER TABLE event_invitation ADD type VARCHAR(20) NOT NULL DEFAULT \'invitation\'');
        $this->addSql('ALTER TABLE event_invitation ADD status VARCHAR(20) NOT NULL DEFAULT \'pending\'');
        $this->addSql('ALTER TABLE event_invitation ADD target_email_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE event_invitation ADD target_phone_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE event_invitation ADD token UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE event_invitation ADD resolved_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');

        // Step 2: Rename target_id to target_user_id and make it nullable
        $this->addSql('ALTER TABLE event_invitation DROP CONSTRAINT IF EXISTS fk_fa0d9336158e0b66');
        $this->addSql('ALTER TABLE event_invitation RENAME COLUMN target_id TO target_user_id');
        $this->addSql('ALTER TABLE event_invitation ALTER COLUMN target_user_id DROP NOT NULL');

        // Step 3: Add foreign key constraints for new columns
        $this->addSql('ALTER TABLE event_invitation ADD CONSTRAINT FK_event_invitation_target_user FOREIGN KEY (target_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event_invitation ADD CONSTRAINT FK_event_invitation_target_email FOREIGN KEY (target_email_id) REFERENCES email (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event_invitation ADD CONSTRAINT FK_event_invitation_target_phone FOREIGN KEY (target_phone_id) REFERENCES phone_number (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Step 4: Add indexes for new columns
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_event_invitation_target_user ON event_invitation (target_user_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_event_invitation_target_email ON event_invitation (target_email_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_event_invitation_target_phone ON event_invitation (target_phone_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_event_invitation_type ON event_invitation (type)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_event_invitation_status ON event_invitation (status)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_event_invitation_token ON event_invitation (token)');

        // Step 5: Migrate data from event_email_invitation to event_invitation (if table exists)
        $this->addSql('
            INSERT INTO event_invitation (id, event_id, owner_id, target_email_id, token, created_at, type, status)
            SELECT id, event_id, owner_id, email_id, token, created_at, \'invitation\', \'pending\'
            FROM event_email_invitation
            WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = \'event_email_invitation\')
            ON CONFLICT DO NOTHING
        ');

        // Step 6: Migrate data from event_request to event_invitation (if table exists)
        $this->addSql('
            INSERT INTO event_invitation (id, event_id, owner_id, created_at, type, status)
            SELECT id, event_id, owner_id, created_at, \'request\', \'pending\'
            FROM event_request
            WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = \'event_request\')
            ON CONFLICT DO NOTHING
        ');

        // Step 7: Add phone_number field to user_contact
        $this->addSql('ALTER TABLE user_contact ADD phone_number_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE user_contact ADD CONSTRAINT FK_user_contact_phone_number FOREIGN KEY (phone_number_id) REFERENCES phone_number (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_user_contact_phone_number ON user_contact (phone_number_id)');

        // Step 8: Drop old tables (after data migration)
        $this->addSql('DROP TABLE IF EXISTS event_email_invitation CASCADE');
        $this->addSql('DROP TABLE IF EXISTS event_request CASCADE');
        $this->addSql('DROP TABLE IF EXISTS event_rejection CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Step 1: Recreate event_email_invitation table
        $this->addSql('
            CREATE TABLE IF NOT EXISTS event_email_invitation (
                id UUID NOT NULL,
                email_id UUID DEFAULT NULL,
                event_id UUID DEFAULT NULL,
                owner_id UUID NOT NULL,
                token UUID NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY (id)
            )
        ');

        // Step 2: Recreate event_request table
        $this->addSql('
            CREATE TABLE IF NOT EXISTS event_request (
                id UUID NOT NULL,
                event_id UUID DEFAULT NULL,
                owner_id UUID DEFAULT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY (id)
            )
        ');

        // Step 3: Recreate event_rejection table
        $this->addSql('
            CREATE TABLE IF NOT EXISTS event_rejection (
                id UUID NOT NULL,
                owner_id UUID NOT NULL,
                event_id UUID DEFAULT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY (id)
            )
        ');

        // Step 4: Remove phone_number from user_contact
        $this->addSql('ALTER TABLE user_contact DROP CONSTRAINT IF EXISTS FK_user_contact_phone_number');
        $this->addSql('DROP INDEX IF EXISTS IDX_user_contact_phone_number');
        $this->addSql('ALTER TABLE user_contact DROP COLUMN IF EXISTS phone_number_id');

        // Step 5: Remove new columns and revert event_invitation
        $this->addSql('ALTER TABLE event_invitation DROP CONSTRAINT IF EXISTS FK_event_invitation_target_user');
        $this->addSql('ALTER TABLE event_invitation DROP CONSTRAINT IF EXISTS FK_event_invitation_target_email');
        $this->addSql('ALTER TABLE event_invitation DROP CONSTRAINT IF EXISTS FK_event_invitation_target_phone');
        $this->addSql('DROP INDEX IF EXISTS IDX_event_invitation_target_user');
        $this->addSql('DROP INDEX IF EXISTS IDX_event_invitation_target_email');
        $this->addSql('DROP INDEX IF EXISTS IDX_event_invitation_target_phone');
        $this->addSql('DROP INDEX IF EXISTS IDX_event_invitation_type');
        $this->addSql('DROP INDEX IF EXISTS IDX_event_invitation_status');
        $this->addSql('DROP INDEX IF EXISTS IDX_event_invitation_token');

        // Remove request type entries (they should go back to event_request)
        $this->addSql('DELETE FROM event_invitation WHERE type = \'request\'');
        // Remove email invitation type entries without target_user (they should go back to event_email_invitation)
        $this->addSql('DELETE FROM event_invitation WHERE type = \'invitation\' AND target_user_id IS NULL AND target_email_id IS NOT NULL');

        $this->addSql('ALTER TABLE event_invitation DROP COLUMN IF EXISTS type');
        $this->addSql('ALTER TABLE event_invitation DROP COLUMN IF EXISTS status');
        $this->addSql('ALTER TABLE event_invitation DROP COLUMN IF EXISTS target_email_id');
        $this->addSql('ALTER TABLE event_invitation DROP COLUMN IF EXISTS target_phone_id');
        $this->addSql('ALTER TABLE event_invitation DROP COLUMN IF EXISTS token');
        $this->addSql('ALTER TABLE event_invitation DROP COLUMN IF EXISTS resolved_at');
        $this->addSql('ALTER TABLE event_invitation RENAME COLUMN target_user_id TO target_id');
        $this->addSql('ALTER TABLE event_invitation ALTER COLUMN target_id SET NOT NULL');
        $this->addSql('ALTER TABLE event_invitation ADD CONSTRAINT fk_fa0d9336158e0b66 FOREIGN KEY (target_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Merge EventOrganiser into EventParticipant with role enum.
 *
 * Replaces the EventOrganiser entity and EventRole lookup table with a single
 * 'role' enum column on EventParticipant. Also replaces the ManyToMany roles
 * on EventOrganiserInvitation with a single 'role' column.
 */
final class Version20250205000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Merge EventOrganiser into EventParticipant with role enum column';
    }

    public function up(Schema $schema): void
    {
        // Step 1: Add role column to event_participant
        $this->addSql("ALTER TABLE event_participant ADD role VARCHAR(255) NOT NULL DEFAULT 'role.event.participant'");

        // Step 2: Migrate existing EventOrganiser rows into event_participant with appropriate role
        // Map old role values to new ones:
        //   role.event.admin -> role.event.organiser
        //   role.event.mod -> role.event.moderator
        //   role.event.promoter -> role.event.promoter (unchanged)
        //   role.event.sponsor -> role.event.sponsor (unchanged)
        $this->addSql("
            INSERT INTO event_participant (id, owner_id, event_id, created_at, role)
            SELECT
                eo.id,
                eo.owner_id,
                eo.event_id,
                eo.created_at,
                COALESCE(
                    (SELECT CASE er.title
                        WHEN 'role.event.admin' THEN 'role.event.organiser'
                        WHEN 'role.event.mod' THEN 'role.event.moderator'
                        WHEN 'role.event.promoter' THEN 'role.event.promoter'
                        WHEN 'role.event.sponsor' THEN 'role.event.sponsor'
                        ELSE 'role.event.organiser'
                    END
                    FROM event_organiser_roles eor
                    JOIN event_role er ON eor.event_role_id = er.id
                    WHERE eor.event_organiser_id = eo.id
                    LIMIT 1),
                    'role.event.organiser'
                )
            FROM event_organiser eo
            WHERE NOT EXISTS (
                SELECT 1 FROM event_participant ep
                WHERE ep.owner_id = eo.owner_id AND ep.event_id = eo.event_id
            )
        ");

        // Step 3: Add role column to event_organiser_invitation
        $this->addSql("ALTER TABLE event_organiser_invitation ADD role VARCHAR(255) NOT NULL DEFAULT 'role.event.organiser'");

        // Step 4: Migrate existing invitation roles
        $this->addSql("
            UPDATE event_organiser_invitation eoi
            SET role = COALESCE(
                (SELECT CASE er.title
                    WHEN 'role.event.admin' THEN 'role.event.organiser'
                    WHEN 'role.event.mod' THEN 'role.event.moderator'
                    WHEN 'role.event.promoter' THEN 'role.event.promoter'
                    WHEN 'role.event.sponsor' THEN 'role.event.sponsor'
                    ELSE 'role.event.organiser'
                END
                FROM event_organiser_invitation_roles eoir
                JOIN event_role er ON eoir.event_role_id = er.id
                WHERE eoir.event_organiser_invitation_id = eoi.id
                LIMIT 1),
                'role.event.organiser'
            )
        ");

        // Step 5: Drop join tables
        $this->addSql('DROP TABLE IF EXISTS event_organiser_roles CASCADE');
        $this->addSql('DROP TABLE IF EXISTS event_participant_roles CASCADE');
        $this->addSql('DROP TABLE IF EXISTS event_organiser_invitation_roles CASCADE');

        // Step 6: Drop event_role table
        $this->addSql('DROP TABLE IF EXISTS event_role CASCADE');

        // Step 7: Drop event_organiser table
        $this->addSql('DROP TABLE IF EXISTS event_organiser CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Step 1: Recreate event_role table
        $this->addSql('
            CREATE TABLE IF NOT EXISTS event_role (
                id UUID NOT NULL,
                event_organiser_id UUID DEFAULT NULL,
                title VARCHAR(255) NOT NULL,
                PRIMARY KEY (id)
            )
        ');

        // Step 2: Recreate event_organiser table
        $this->addSql('
            CREATE TABLE IF NOT EXISTS event_organiser (
                id UUID NOT NULL,
                owner_id UUID DEFAULT NULL,
                event_id UUID NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY (id)
            )
        ');
        $this->addSql('ALTER TABLE event_organiser ADD CONSTRAINT fk_event_organiser_owner FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event_organiser ADD CONSTRAINT fk_event_organiser_event FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Step 3: Recreate join tables
        $this->addSql('
            CREATE TABLE IF NOT EXISTS event_organiser_roles (
                event_organiser_id UUID NOT NULL,
                event_role_id UUID NOT NULL,
                PRIMARY KEY (event_organiser_id, event_role_id)
            )
        ');

        $this->addSql('
            CREATE TABLE IF NOT EXISTS event_participant_roles (
                event_participant_id UUID NOT NULL,
                event_role_id UUID NOT NULL,
                PRIMARY KEY (event_participant_id, event_role_id)
            )
        ');

        $this->addSql('
            CREATE TABLE IF NOT EXISTS event_organiser_invitation_roles (
                event_organiser_invitation_id UUID NOT NULL,
                event_role_id UUID NOT NULL,
                PRIMARY KEY (event_organiser_invitation_id, event_role_id)
            )
        ');

        // Step 4: Remove role columns
        $this->addSql('ALTER TABLE event_participant DROP COLUMN IF EXISTS role');
        $this->addSql('ALTER TABLE event_organiser_invitation DROP COLUMN IF EXISTS role');
    }
}

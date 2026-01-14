<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Email column already exists – migration marked as executed.
 */
final class Version20260113165804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Email column already present; no schema changes required';
    }

    public function up(Schema $schema): void
    {
        // NO-OP
        // The email column already exists in the database.
    }

    public function down(Schema $schema): void
    {
        // NO-OP
    }
}

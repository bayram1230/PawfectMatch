<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260113003106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add status column to pets table with default ACTIVE';
    }

    public function up(Schema $schema): void
    {
        // Add status column with a safe default
        $this->addSql(
            "ALTER TABLE pets ADD status VARCHAR(255) NOT NULL DEFAULT 'active'"
        );

        // Ensure existing rows are initialized correctly
        $this->addSql(
            "UPDATE pets SET status = 'active' WHERE status IS NULL"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE pets DROP status');
    }
}

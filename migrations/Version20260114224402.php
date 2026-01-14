<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260114224402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adoption_request ADD applicant_id INT NOT NULL, DROP username');
        $this->addSql('ALTER TABLE adoption_request ADD CONSTRAINT FK_410896EE97139001 FOREIGN KEY (applicant_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_410896EE97139001 ON adoption_request (applicant_id)');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_USERNAME ON user');
        $this->addSql('ALTER TABLE user ADD email VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adoption_request DROP FOREIGN KEY FK_410896EE97139001');
        $this->addSql('DROP INDEX IDX_410896EE97139001 ON adoption_request');
        $this->addSql('ALTER TABLE adoption_request ADD username VARCHAR(50) NOT NULL, DROP applicant_id');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON user');
        $this->addSql('ALTER TABLE user DROP email');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON user (username)');
    }
}

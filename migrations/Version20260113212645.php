<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260113212645 extends AbstractMigration
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
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adoption_request DROP FOREIGN KEY FK_410896EE97139001');
        $this->addSql('DROP INDEX IDX_410896EE97139001 ON adoption_request');
        $this->addSql('ALTER TABLE adoption_request ADD username VARCHAR(50) NOT NULL, DROP applicant_id');
    }
}

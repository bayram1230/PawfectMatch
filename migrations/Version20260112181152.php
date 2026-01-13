<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260112181152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pet_of_week (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, pet_id INT NOT NULL, INDEX IDX_E02C6115966F7FB6 (pet_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE pet_of_week ADD CONSTRAINT FK_E02C6115966F7FB6 FOREIGN KEY (pet_id) REFERENCES pets (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pet_of_week DROP FOREIGN KEY FK_E02C6115966F7FB6');
        $this->addSql('DROP TABLE pet_of_week');
    }
}

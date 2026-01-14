<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260111212423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adoption_history (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(20) NOT NULL, decided_at DATETIME NOT NULL, note LONGTEXT DEFAULT NULL, adoption_request_id INT NOT NULL, decided_by_id INT DEFAULT NULL, INDEX IDX_5D25693AECFD9D75 (adoption_request_id), INDEX IDX_5D25693AE26B496B (decided_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE adoption_request (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, message LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, username VARCHAR(50) NOT NULL, survey_answer LONGTEXT DEFAULT NULL, pet_id INT NOT NULL, INDEX IDX_410896EE966F7FB6 (pet_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE pets (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, species VARCHAR(100) NOT NULL, age INT NOT NULL, image VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(20) NOT NULL, breed VARCHAR(100) DEFAULT NULL, sex VARCHAR(20) DEFAULT NULL, color VARCHAR(50) DEFAULT NULL, size VARCHAR(50) DEFAULT NULL, adoption_requirements LONGTEXT DEFAULT NULL, shelter_id INT DEFAULT NULL, INDEX IDX_8638EA3F54053EC0 (shelter_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE adoption_history ADD CONSTRAINT FK_5D25693AECFD9D75 FOREIGN KEY (adoption_request_id) REFERENCES adoption_request (id)');
        $this->addSql('ALTER TABLE adoption_history ADD CONSTRAINT FK_5D25693AE26B496B FOREIGN KEY (decided_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE adoption_request ADD CONSTRAINT FK_410896EE966F7FB6 FOREIGN KEY (pet_id) REFERENCES pets (id)');
        $this->addSql('ALTER TABLE pets ADD CONSTRAINT FK_8638EA3F54053EC0 FOREIGN KEY (shelter_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adoption_history DROP FOREIGN KEY FK_5D25693AECFD9D75');
        $this->addSql('ALTER TABLE adoption_history DROP FOREIGN KEY FK_5D25693AE26B496B');
        $this->addSql('ALTER TABLE adoption_request DROP FOREIGN KEY FK_410896EE966F7FB6');
        $this->addSql('ALTER TABLE pets DROP FOREIGN KEY FK_8638EA3F54053EC0');
        $this->addSql('DROP TABLE adoption_history');
        $this->addSql('DROP TABLE adoption_request');
        $this->addSql('DROP TABLE pets');
        $this->addSql('DROP TABLE user');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240206163249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favicon (id INT AUTO_INCREMENT NOT NULL, size VARCHAR(255) NOT NULL, safe_file_name VARCHAR(255) NOT NULL, extension VARCHAR(255) NOT NULL, image_id INT NOT NULL, INDEX IDX_84E099F63DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE favicon ADD CONSTRAINT FK_84E099F63DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favicon DROP FOREIGN KEY FK_84E099F63DA5256D');
        $this->addSql('DROP TABLE favicon');
    }
}

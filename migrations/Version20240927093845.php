<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240927093845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ALTER main_image1 DROP DEFAULT');
        $this->addSql('ALTER TABLE article ALTER main_image2 DROP DEFAULT');
        $this->addSql('ALTER TABLE category ADD slug VARCHAR(255) NOT NULL DEFAULT \'\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE category DROP slug');
        $this->addSql('ALTER TABLE article ALTER main_image1 SET DEFAULT \'\'');
        $this->addSql('ALTER TABLE article ALTER main_image2 SET DEFAULT \'\'');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240928134442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_article DROP CONSTRAINT fk_c5e24e1812469de2');
        $this->addSql('ALTER TABLE category_article DROP CONSTRAINT fk_c5e24e187294869c');
        $this->addSql('DROP TABLE category_article');
        $this->addSql('ALTER TABLE article ADD category_id INT NOT NULL DEFAULT 16');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_23A0E6612469DE2 ON article (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE category_article (category_id INT NOT NULL, article_id INT NOT NULL, PRIMARY KEY(category_id, article_id))');
        $this->addSql('CREATE INDEX idx_c5e24e187294869c ON category_article (article_id)');
        $this->addSql('CREATE INDEX idx_c5e24e1812469de2 ON category_article (category_id)');
        $this->addSql('ALTER TABLE category_article ADD CONSTRAINT fk_c5e24e1812469de2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_article ADD CONSTRAINT fk_c5e24e187294869c FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E6612469DE2');
        $this->addSql('DROP INDEX IDX_23A0E6612469DE2');
        $this->addSql('ALTER TABLE article DROP category_id');
    }
}

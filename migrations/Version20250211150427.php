<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250211150427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_skill ADD levels_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_skill ADD CONSTRAINT FK_BCFF1F2FAF9C3A25 FOREIGN KEY (levels_id) REFERENCES level (id)');
        $this->addSql('CREATE INDEX IDX_BCFF1F2FAF9C3A25 ON user_skill (levels_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_skill DROP FOREIGN KEY FK_BCFF1F2FAF9C3A25');
        $this->addSql('DROP INDEX IDX_BCFF1F2FAF9C3A25 ON user_skill');
        $this->addSql('ALTER TABLE user_skill DROP levels_id');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250317211346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_project_likes (user_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_B411EAB3A76ED395 (user_id), INDEX IDX_B411EAB3166D1F9C (project_id), PRIMARY KEY(user_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_project_likes ADD CONSTRAINT FK_B411EAB3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_project_likes ADD CONSTRAINT FK_B411EAB3166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_project_likes DROP FOREIGN KEY FK_B411EAB3A76ED395');
        $this->addSql('ALTER TABLE user_project_likes DROP FOREIGN KEY FK_B411EAB3166D1F9C');
        $this->addSql('DROP TABLE user_project_likes');
    }
}

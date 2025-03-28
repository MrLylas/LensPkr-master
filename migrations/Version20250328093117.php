<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250328093117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job ADD job_speciality_id INT NOT NULL');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F89084561B FOREIGN KEY (job_speciality_id) REFERENCES speciality (id)');
        $this->addSql('CREATE INDEX IDX_FBD8E0F89084561B ON job (job_speciality_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F89084561B');
        $this->addSql('DROP INDEX IDX_FBD8E0F89084561B ON job');
        $this->addSql('ALTER TABLE job DROP job_speciality_id');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413200424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project ADD speciality_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE3B5A08D7 FOREIGN KEY (speciality_id) REFERENCES speciality (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE3B5A08D7 ON project (speciality_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE3B5A08D7');
        $this->addSql('DROP INDEX IDX_2FB3D0EE3B5A08D7 ON project');
        $this->addSql('ALTER TABLE project DROP speciality_id');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210125154637 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task ADD work_time_id INT NOT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB258B216519 FOREIGN KEY (work_time_id) REFERENCES work_time (id)');
        $this->addSql('CREATE INDEX IDX_527EDB258B216519 ON task (work_time_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB258B216519');
        $this->addSql('DROP INDEX IDX_527EDB258B216519 ON task');
        $this->addSql('ALTER TABLE task DROP work_time_id');
    }
}

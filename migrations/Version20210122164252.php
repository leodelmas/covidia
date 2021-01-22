<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210122164252 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, job_id INT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, birth_date DATE NOT NULL, hiring_date DATE NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(10) NOT NULL, is_executive TINYINT(1) NOT NULL, is_admin TINYINT(1) NOT NULL, INDEX IDX_8D93D649BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_time (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, date_start DATE NOT NULL, date_end DATE NOT NULL, is_teleworked TINYINT(1) NOT NULL, INDEX IDX_9657297DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE work_time ADD CONSTRAINT FK_9657297DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BE04EA9');
        $this->addSql('ALTER TABLE work_time DROP FOREIGN KEY FK_9657297DA76ED395');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE work_time');
    }
}

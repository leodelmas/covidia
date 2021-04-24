<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210424141859 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sos (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, message LONGTEXT NOT NULL, is_anonymous TINYINT(1) NOT NULL, is_closed TINYINT(1) NOT NULL, INDEX IDX_829F58F5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, work_time_id INT NOT NULL, task_category_id INT NOT NULL, date_time_start DATETIME NOT NULL, date_time_end DATETIME NOT NULL, comment LONGTEXT NOT NULL, INDEX IDX_527EDB25A76ED395 (user_id), INDEX IDX_527EDB258B216519 (work_time_id), INDEX IDX_527EDB25543330D0 (task_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_remote TINYINT(1) NOT NULL, is_physical TINYINT(1) NOT NULL, color VARCHAR(7) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, job_id INT NOT NULL, updated_at DATETIME DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, birth_date DATE NOT NULL, hiring_date DATE NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(10) NOT NULL, is_executive TINYINT(1) NOT NULL, is_psychologist TINYINT(1) NOT NULL, is_admin TINYINT(1) NOT NULL, file_name VARCHAR(255) DEFAULT NULL, INDEX IDX_8D93D649BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_time (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, date_start DATE NOT NULL, date_end DATE NOT NULL, is_teleworked TINYINT(1) NOT NULL, INDEX IDX_9657297DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sos ADD CONSTRAINT FK_829F58F5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB258B216519 FOREIGN KEY (work_time_id) REFERENCES work_time (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25543330D0 FOREIGN KEY (task_category_id) REFERENCES task_category (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE work_time ADD CONSTRAINT FK_9657297DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BE04EA9');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25543330D0');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE sos DROP FOREIGN KEY FK_829F58F5A76ED395');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25A76ED395');
        $this->addSql('ALTER TABLE work_time DROP FOREIGN KEY FK_9657297DA76ED395');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB258B216519');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE sos');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_category');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE work_time');
    }
}

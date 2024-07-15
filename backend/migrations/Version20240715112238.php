<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240715112238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agenda (id INT AUTO_INCREMENT NOT NULL, interview_id INT NOT NULL, date DATETIME NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, status VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_2CEDC87755D69D95 (interview_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE appreciation (id INT AUTO_INCREMENT NOT NULL, interview_id INT DEFAULT NULL, comment LONGTEXT NOT NULL, score INT NOT NULL, INDEX IDX_5CD4DEAB55D69D95 (interview_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidate (id INT AUTO_INCREMENT NOT NULL, resume_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, hire_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_C8B28E44E7927C74 (email), UNIQUE INDEX UNIQ_C8B28E44D262AF09 (resume_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidate_phase (id INT AUTO_INCREMENT NOT NULL, candidate_id INT DEFAULT NULL, phase VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, result VARCHAR(255) NOT NULL, INDEX IDX_895D3D3791BD8781 (candidate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluator (id INT NOT NULL, specialization VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hrmanager (id INT NOT NULL, department VARCHAR(255) NOT NULL, position VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interview (id INT AUTO_INCREMENT NOT NULL, candidate_id INT NOT NULL, evaluator_id INT NOT NULL, hr_manager_id INT NOT NULL, interview_date DATETIME NOT NULL, interview_location VARCHAR(255) NOT NULL, INDEX IDX_CF1D3C3491BD8781 (candidate_id), INDEX IDX_CF1D3C3443575BE2 (evaluator_id), INDEX IDX_CF1D3C34E307252D (hr_manager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE interview_status (id INT AUTO_INCREMENT NOT NULL, interview_id INT NOT NULL, status VARCHAR(255) NOT NULL, status_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_1BF9696B55D69D95 (interview_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user__id INT NOT NULL, content VARCHAR(255) NOT NULL, notification_date DATETIME NOT NULL, INDEX IDX_BF5476CA8D57A4BB (user__id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resume (id INT AUTO_INCREMENT NOT NULL, file_path VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, dType VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC87755D69D95 FOREIGN KEY (interview_id) REFERENCES interview (id)');
        $this->addSql('ALTER TABLE appreciation ADD CONSTRAINT FK_5CD4DEAB55D69D95 FOREIGN KEY (interview_id) REFERENCES interview (id)');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E44D262AF09 FOREIGN KEY (resume_id) REFERENCES resume (id)');
        $this->addSql('ALTER TABLE candidate_phase ADD CONSTRAINT FK_895D3D3791BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id)');
        $this->addSql('ALTER TABLE evaluator ADD CONSTRAINT FK_750B980FBF396750 FOREIGN KEY (id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hrmanager ADD CONSTRAINT FK_888F5B38BF396750 FOREIGN KEY (id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C3491BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id)');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C3443575BE2 FOREIGN KEY (evaluator_id) REFERENCES evaluator (id)');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C34E307252D FOREIGN KEY (hr_manager_id) REFERENCES hrmanager (id)');
        $this->addSql('ALTER TABLE interview_status ADD CONSTRAINT FK_1BF9696B55D69D95 FOREIGN KEY (interview_id) REFERENCES interview (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA8D57A4BB FOREIGN KEY (user__id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agenda DROP FOREIGN KEY FK_2CEDC87755D69D95');
        $this->addSql('ALTER TABLE appreciation DROP FOREIGN KEY FK_5CD4DEAB55D69D95');
        $this->addSql('ALTER TABLE candidate DROP FOREIGN KEY FK_C8B28E44D262AF09');
        $this->addSql('ALTER TABLE candidate_phase DROP FOREIGN KEY FK_895D3D3791BD8781');
        $this->addSql('ALTER TABLE evaluator DROP FOREIGN KEY FK_750B980FBF396750');
        $this->addSql('ALTER TABLE hrmanager DROP FOREIGN KEY FK_888F5B38BF396750');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C3491BD8781');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C3443575BE2');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C34E307252D');
        $this->addSql('ALTER TABLE interview_status DROP FOREIGN KEY FK_1BF9696B55D69D95');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA8D57A4BB');
        $this->addSql('DROP TABLE agenda');
        $this->addSql('DROP TABLE appreciation');
        $this->addSql('DROP TABLE candidate');
        $this->addSql('DROP TABLE candidate_phase');
        $this->addSql('DROP TABLE evaluator');
        $this->addSql('DROP TABLE hrmanager');
        $this->addSql('DROP TABLE interview');
        $this->addSql('DROP TABLE interview_status');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE resume');
        $this->addSql('DROP TABLE `user`');
    }
}

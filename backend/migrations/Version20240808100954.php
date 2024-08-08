<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240808100954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evaluator_interview (evaluator_id INT NOT NULL, interview_id INT NOT NULL, INDEX IDX_E1FDC55843575BE2 (evaluator_id), INDEX IDX_E1FDC55855D69D95 (interview_id), PRIMARY KEY(evaluator_id, interview_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evaluator_interview ADD CONSTRAINT FK_E1FDC55843575BE2 FOREIGN KEY (evaluator_id) REFERENCES evaluator (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evaluator_interview ADD CONSTRAINT FK_E1FDC55855D69D95 FOREIGN KEY (interview_id) REFERENCES interview (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C3443575BE2');
        $this->addSql('DROP INDEX IDX_CF1D3C3443575BE2 ON interview');
        $this->addSql('ALTER TABLE interview DROP evaluator_id');
        $this->addSql('ALTER TABLE resume CHANGE file_path file_path VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evaluator_interview DROP FOREIGN KEY FK_E1FDC55843575BE2');
        $this->addSql('ALTER TABLE evaluator_interview DROP FOREIGN KEY FK_E1FDC55855D69D95');
        $this->addSql('DROP TABLE evaluator_interview');
        $this->addSql('ALTER TABLE interview ADD evaluator_id INT NOT NULL');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C3443575BE2 FOREIGN KEY (evaluator_id) REFERENCES evaluator (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_CF1D3C3443575BE2 ON interview (evaluator_id)');
        $this->addSql('ALTER TABLE resume CHANGE file_path file_path VARCHAR(255) NOT NULL');
    }
}

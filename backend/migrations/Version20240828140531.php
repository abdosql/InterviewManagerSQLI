<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240828140531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interview ADD interview_status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C34529D7BBC FOREIGN KEY (interview_status_id) REFERENCES interview_status (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CF1D3C34529D7BBC ON interview (interview_status_id)');
        $this->addSql('ALTER TABLE interview_status DROP FOREIGN KEY FK_1BF9696B55D69D95');
        $this->addSql('DROP INDEX UNIQ_1BF9696B55D69D95 ON interview_status');
        $this->addSql('ALTER TABLE interview_status DROP interview_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interview_status ADD interview_id INT NOT NULL');
        $this->addSql('ALTER TABLE interview_status ADD CONSTRAINT FK_1BF9696B55D69D95 FOREIGN KEY (interview_id) REFERENCES interview (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1BF9696B55D69D95 ON interview_status (interview_id)');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C34529D7BBC');
        $this->addSql('DROP INDEX UNIQ_CF1D3C34529D7BBC ON interview');
        $this->addSql('ALTER TABLE interview DROP interview_status_id');
    }
}

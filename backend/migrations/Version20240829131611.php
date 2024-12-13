<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240829131611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interview_status DROP FOREIGN KEY FK_1BF9696B55D69D95');
        $this->addSql('ALTER TABLE interview_status CHANGE interview_id interview_id INT NOT NULL');
        $this->addSql('ALTER TABLE interview_status ADD CONSTRAINT FK_1BF9696B55D69D95 FOREIGN KEY (interview_id) REFERENCES interview (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interview_status DROP FOREIGN KEY FK_1BF9696B55D69D95');
        $this->addSql('ALTER TABLE interview_status CHANGE interview_id interview_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE interview_status ADD CONSTRAINT FK_1BF9696B55D69D95 FOREIGN KEY (interview_id) REFERENCES interview (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200520083616 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tour_category (id INT NOT NULL, name VARCHAR(128) NOT NULL, sort INT DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, UNIQUE INDEX UNIQ_9CB340F25E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tour ADD tour_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tour ADD CONSTRAINT FK_6AD1F9697210CF21 FOREIGN KEY (tour_category_id) REFERENCES tour_category (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_6AD1F9697210CF21 ON tour (tour_category_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tour DROP FOREIGN KEY FK_6AD1F9697210CF21');
        $this->addSql('DROP TABLE tour_category');
        $this->addSql('DROP INDEX IDX_6AD1F9697210CF21 ON tour');
        $this->addSql('ALTER TABLE tour DROP tour_category_id');
    }
}

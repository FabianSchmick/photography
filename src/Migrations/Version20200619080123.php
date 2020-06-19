<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200619080123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE location_to_tour (tour_id INT NOT NULL, location_id INT NOT NULL, INDEX IDX_637AFB0215ED8D43 (tour_id), INDEX IDX_637AFB0264D218E (location_id), PRIMARY KEY(tour_id, location_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE location_to_tour ADD CONSTRAINT FK_637AFB0215ED8D43 FOREIGN KEY (tour_id) REFERENCES tour (id)');
        $this->addSql('ALTER TABLE location_to_tour ADD CONSTRAINT FK_637AFB0264D218E FOREIGN KEY (location_id) REFERENCES location (id)');

        // Migrate data without losing it
        $this->addSql('INSERT INTO location_to_tour (tour_id, location_id) SELECT id, location_id FROM tour WHERE location_id IS NOT NULL');

        $this->addSql('ALTER TABLE tour DROP FOREIGN KEY FK_6AD1F96964D218E');
        $this->addSql('DROP INDEX IDX_6AD1F96964D218E ON tour');
        $this->addSql('ALTER TABLE tour DROP location_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE location_to_tour');
        $this->addSql('ALTER TABLE tour ADD location_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tour ADD CONSTRAINT FK_6AD1F96964D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_6AD1F96964D218E ON tour (location_id)');
    }
}

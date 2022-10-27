<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181221185425 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D7064D218E');
        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D70F675F31B');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D7064D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D70F675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D70F675F31B');
        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D7064D218E');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D70F675F31B FOREIGN KEY (author_id) REFERENCES author (id)');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D7064D218E FOREIGN KEY (location_id) REFERENCES location (id)');
    }
}

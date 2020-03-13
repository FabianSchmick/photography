<?php declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181223135451 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tour (id INT NOT NULL, preview_entry_id INT DEFAULT NULL, file_id BIGINT DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, slug VARCHAR(255) NOT NULL, updated DATETIME NOT NULL, UNIQUE INDEX UNIQ_6AD1F9695E237E06 (name), UNIQUE INDEX UNIQ_6AD1F969989D9B62 (slug), UNIQUE INDEX UNIQ_6AD1F9697D7A3BE2 (preview_entry_id), UNIQUE INDEX UNIQ_6AD1F96993CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tour ADD CONSTRAINT FK_6AD1F9697D7A3BE2 FOREIGN KEY (preview_entry_id) REFERENCES entry (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE tour ADD CONSTRAINT FK_6AD1F96993CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE entry ADD tour_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D7015ED8D43 FOREIGN KEY (tour_id) REFERENCES tour (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_2B219D7015ED8D43 ON entry (tour_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D7015ED8D43');
        $this->addSql('DROP TABLE tour');
        $this->addSql('DROP INDEX IDX_2B219D7015ED8D43 ON entry');
        $this->addSql('ALTER TABLE entry DROP tour_id');
    }
}

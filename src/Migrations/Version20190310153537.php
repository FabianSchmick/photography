<?php declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190310153537 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B7833DA5256D');
        $this->addSql('DROP INDEX UNIQ_389B7833DA5256D ON tag');
        $this->addSql('ALTER TABLE tag ADD preview_entry_id INT DEFAULT NULL, DROP image_id');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B7837D7A3BE2 FOREIGN KEY (preview_entry_id) REFERENCES entry (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B7837D7A3BE2 ON tag (preview_entry_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B7837D7A3BE2');
        $this->addSql('DROP INDEX UNIQ_389B7837D7A3BE2 ON tag');
        $this->addSql('ALTER TABLE tag ADD image_id BIGINT DEFAULT NULL, DROP preview_entry_id');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B7833DA5256D FOREIGN KEY (image_id) REFERENCES file (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B7833DA5256D ON tag (image_id)');
    }
}

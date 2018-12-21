<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181221184249 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(255) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, UNIQUE INDEX UNIQ_BDAFD8C85E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entry (id INT NOT NULL, author_id INT DEFAULT NULL, image_id BIGINT DEFAULT NULL, location_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, timestamp DATETIME DEFAULT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_2B219D70989D9B62 (slug), INDEX IDX_2B219D70F675F31B (author_id), UNIQUE INDEX UNIQ_2B219D703DA5256D (image_id), INDEX IDX_2B219D7064D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_to_entry (entry_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_162CD79BBA364942 (entry_id), INDEX IDX_162CD79BBAD26311 (tag_id), PRIMARY KEY(entry_id, tag_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id BIGINT AUTO_INCREMENT NOT NULL, file_name VARCHAR(255) DEFAULT NULL, original_name VARCHAR(255) DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, sti_descriminator VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5E9E89CB5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, image_id BIGINT DEFAULT NULL, name VARCHAR(128) NOT NULL, description TEXT DEFAULT NULL, sort INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_389B7835E237E06 (name), UNIQUE INDEX UNIQ_389B783989D9B62 (slug), UNIQUE INDEX UNIQ_389B7833DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D70F675F31B FOREIGN KEY (author_id) REFERENCES author (id)');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D703DA5256D FOREIGN KEY (image_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D7064D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE tag_to_entry ADD CONSTRAINT FK_162CD79BBA364942 FOREIGN KEY (entry_id) REFERENCES entry (id)');
        $this->addSql('ALTER TABLE tag_to_entry ADD CONSTRAINT FK_162CD79BBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B7833DA5256D FOREIGN KEY (image_id) REFERENCES file (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D70F675F31B');
        $this->addSql('ALTER TABLE tag_to_entry DROP FOREIGN KEY FK_162CD79BBA364942');
        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D703DA5256D');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B7833DA5256D');
        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D7064D218E');
        $this->addSql('ALTER TABLE tag_to_entry DROP FOREIGN KEY FK_162CD79BBAD26311');
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE entry');
        $this->addSql('DROP TABLE tag_to_entry');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE tag');
    }
}

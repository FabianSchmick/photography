<?php declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190310145353 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE entry ADD created DATETIME NOT NULL DEFAULT NOW(), ADD updated DATETIME NOT NULL DEFAULT NOW()');
        $this->addSql('ALTER TABLE tag ADD created DATETIME NOT NULL DEFAULT NOW(), ADD updated DATETIME NOT NULL DEFAULT NOW()');
        $this->addSql('ALTER TABLE tour ADD created DATETIME NOT NULL DEFAULT NOW()');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE entry DROP created, DROP updated');
        $this->addSql('ALTER TABLE tag DROP created, DROP updated');
        $this->addSql('ALTER TABLE tour DROP created');
    }
}

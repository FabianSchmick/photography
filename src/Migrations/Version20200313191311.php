<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200313191311 extends AbstractMigration
{
    private $entities = ['Author', 'Entry', 'Location', 'Tag', 'Tour'];

    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        foreach ($this->entities as $entity) {
            $sql = "UPDATE ext_translations SET object_class='App\\\\Entity\\\\{$entity}' WHERE object_class= 'AppBundle\\\\Entity\\\\{$entity}'";
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        foreach ($this->entities as $entity) {
            $this->addSql("UPDATE ext_translations SET object_class='AppBundle\\\\Entity\\\\{$entity}' WHERE object_class= 'App\\\\Entity\\\\{$entity}'");
        }
    }
}

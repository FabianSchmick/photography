<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190310145353 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE entry ADD created DATETIME NOT NULL DEFAULT NOW(), ADD updated DATETIME NOT NULL DEFAULT NOW()');
        $this->addSql('ALTER TABLE tag ADD created DATETIME NOT NULL DEFAULT NOW(), ADD updated DATETIME NOT NULL DEFAULT NOW()');
        $this->addSql('ALTER TABLE tour ADD created DATETIME NOT NULL DEFAULT NOW()');
    }

    public function postUp(Schema $schema)
    {
        $entries = $this->em->getRepository('AppBundle:Entry')->findAll();
        foreach ($entries as $entry) {
            $entry->setCreated($entry->getTimestamp());
            $this->em->persist($entry);
        }

        $tags = $this->em->getRepository('AppBundle:Tag')->findAll();
        foreach ($tags as $tag) {
            $tag->setCreated(new \DateTime());
            $this->em->persist($tag);
        }

        $tours = $this->em->getRepository('AppBundle:Tour')->findAll();
        foreach ($tours as $tour) {
            $tour->setCreated($tour->getUpdated());
            $this->em->persist($tour);
        }

        $this->em->flush();
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

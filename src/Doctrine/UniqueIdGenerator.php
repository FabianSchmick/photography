<?php

namespace App\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

class UniqueIdGenerator extends AbstractIdGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate(EntityManager $em, $entity): int
    {
        do {
            $id = random_int(10_000_000, 99_999_999);
            $found = $em->getRepository(get_class($entity))->findOneBy(['id' => $id]);

            if (!$found) {
                $persisted = $em->getUnitOfWork()->getScheduledEntityInsertions();
                $ids = array_map(fn ($o) => $o->getId(), $persisted);
                $found = array_search($id, $ids);
            }
        } while ($found);

        return $id;
    }
}

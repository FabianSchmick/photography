<?php

namespace AppBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

class UniqueIdGenerator extends AbstractIdGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate(EntityManager $em, $entity)
    {
        do {
            $id = random_int(10000000, 99999999);
            $found = $em->getRepository(get_class($entity))->findOneBy(['id' => $id]);

            if (!$found) {
                $persisted = $em->getUnitOfWork()->getScheduledEntityInsertions();
                $ids = array_map(function ($o) { return $o->getId(); }, $persisted);
                $found = array_search($id, $ids);
            }
        } while ($found);

        return $id;
    }
}

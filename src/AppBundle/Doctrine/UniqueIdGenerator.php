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
        } while ($found);

        return $id;
    }
}

<?php

namespace Darles\Bundle\ForumBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Darles\Bundle\ForumBundle\Model\RepositoryInterface;

abstract class ObjectRepository extends EntityRepository implements RepositoryInterface
{

    /**
     * @see RepositoryInterface::getObjectManager
     */
    public function getObjectManager()
    {
        return $this->getEntityManager();
    }

    /**
     * @see RepositoryInterface::getObjectClass
     */
    public function getObjectClass()
    {
        return $this->getEntityName();
    }

    /**
     * @see RepositoryInterface::getObjectIdentifier
     */
    public function getObjectIdentifier()
    {
        return reset($this->getClassMetadata()->identifier);
    }

    /**
     * @see RepositoryInterface::cleanUp
     */
    public function cleanUp()
    {
        $this->createQueryBuilder('o')->delete()->getQuery()->execute();
    }

}

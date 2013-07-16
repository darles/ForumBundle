<?php

namespace Darles\Bundle\ForumBundle\Entity;

use Darles\Bundle\ForumBundle\Model\CategoryRepositoryInterface;

class CategoryRepository extends ObjectRepository implements CategoryRepositoryInterface
{

    /**
     * @see CategoryRepositoryInterface::findOneBySlug
     */
    public function findOneBySlug($slug)
    {
        return $this->findOneBy(array('slug' => $slug));
    }

    /**
     * @see CategoryRepositoryInteface::findAll
     */
    public function findAllWithPagination(\Knp\Component\Pager\Paginator $paginator, $page, $limit)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->orderBy('c.position');

        return $paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );
    }

    /**
     * @see CategoryRepositoryInterface::findAllIndexById
     */
    public function findAllIndexById()
    {
        return $this->getObjectManager()
            ->createQuery(sprintf('SELECT category FROM %s category INDEX BY category.id ORDER BY category.position', $this->getObjectClass()))
            ->execute();
    }
}

<?php

namespace Darles\Bundle\ForumBundle\Entity;

use Darles\Bundle\ForumBundle\Model\TopicRepositoryInterface;

class TopicRepository extends ObjectRepository implements TopicRepositoryInterface
{
    /**
     * @see TopicRepositoryInterface::findOneByCategoryAndSlug
     */
    public function findOneByCategoryAndSlug($category, $slug)
    {
        return $this->findOneBy(array(
            'slug' => $slug,
            'category' => $category->getId()
        ));
    }

    /**
     * @see TopicRepositoryInterface::findOneById
     */
    public function findOneById($id)
    {
        return $this->findOneBy(array('id' => $id));
    }

    /**
     * @see TopicRepositoryInterface::findAll
     */
    public function findAllWithPagination(\Knp\Component\Pager\Paginator $paginator, $page, $limit)
    {
        $qb = $this->createQueryBuilder('topic')
            ->orderBy('topic.isPinned', 'DESC')
            ->addOrderBy('topic.pulledAt', 'DESC');

        return $paginator->paginate(
            $qb->getquery(),
            $page,
            $limit
        );
    }

    /**
     * @see TopicRepositoryInterface::findAllByCategory
     */
    public function findAllByCategory($category, \Knp\Component\Pager\Paginator $paginator, $page, $limit)
    {
        $qb = $this->createQueryBuilder('topic');
        $qb->orderBy('topic.isPinned', 'DESC')
            ->addOrderBy('topic.pulledAt', 'DESC')
            ->where($qb->expr()->eq('topic.category', $category->getId()));

        return $paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );
    }

    /**
     * @see TopicRepositoryInterface::findLatestPosted
     */
    public function findLatestPosted($number)
    {
        return $this->createQueryBuilder('topic')
            ->orderBy('topic.pulledAt', 'DESC')
            ->setMaxResults($number)
            ->getQuery()
            ->execute();
    }

    /**
     * @see TopicRepositoryInterface::search
     */
    public function search($query, \Knp\Component\Pager\Paginator $paginator, $page, $limit)
    {
        $qb = $this->createQueryBuilder('topic');
        $qb->orderBy('topic.pulledAt DESC')->where($qb->expr()->like('topic.subject', '%' . $query . '%'));

        return $paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );
    }

    /**
     * @see TopicRepositoryInterface::incrementTopicNumViews
     */
    public function incrementTopicNumViews($topic)
    {
        $this->createQueryBuilder('topic')
            ->update()
            ->set('topic.numViews', 'topic.numViews + 1')
            ->where('topic.id = :topic_id')
            ->setParameter('topic_id', $topic->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * @see TopicRepositoryInterface::createNewTopic
     */
    public function createNewTopic()
    {
        $class = $this->getObjectClass();

        return new $class();
    }
}

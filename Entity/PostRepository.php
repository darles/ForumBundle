<?php

namespace Darles\Bundle\ForumBundle\Entity;

use Darles\Bundle\ForumBundle\Model\PostRepositoryInterface;

class PostRepository extends ObjectRepository implements PostRepositoryInterface
{
    /**
     * @see PostRepositoryInterface::findOneById
     */
    public function findOneById($id)
    {
        return $this->findOneBy(array('id' => $id));
    }

    /**
     * @see PostRepositoryInterface::findAllByTopic
     */
    public function findAllByTopic($topic, \Knp\Component\Pager\Paginator $paginator = null, $page = 0, $limit = 0)
    {
        $qb = $this->createQueryBuilder('post')
            ->orderBy('post.createdAt')
            ->where('post.topic = :topic')
            ->setParameter('topic', $topic->getId());

        if (is_null($paginator)) {
            return $qb->getQuery()->execute();
        } else {
            return $paginator->paginate(
                $qb->getQuery(),
                $page,
                $limit
            );
        }
    }

    /**
     * @see PostRepositoryInterface::findRecentPosts
     */
    public function findRecentPosts(\Knp\Component\Pager\Paginator $paginator, $page, $limit)
    {
        $qb = $this->createQueryBuilder('post')
            ->orderBy('post.createdAt', 'DESC')
            ->groupBy('post.topic');

        return $paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit);
    }

    /**
     * @see PostRepositoryInterface::findRecentByTopic
     */
    public function findRecentByTopic($topic, $number)
    {
        return $this->createQueryBuilder('post')
            ->orderBy('post.createdAt', 'DESC')
            ->where('post.topic = :topic')
            ->setMaxResults($number)
            ->setParameter('topic', $topic->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * @see PostRepositoryInterface::search
     */
    public function search($query, \Knp\Component\Pager\Paginator $paginator, $page, $limit)
    {
        $qb = $this->createQueryBuilder('post');
        $qb
            ->where($qb->expr()->like('post.message', $qb->expr()->literal('%' . $query . '%')))
            ->orderBy('post.createdAt');

        return $paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit);
    }

    /**
     * Gets the post that preceds this one
     *
     * @return Post or null
     **/
    public function getPostBefore($post)
    {
        $candidate = null;
        foreach ($this->findAllByTopic($post->getTopic()) as $p) {
            if ($p !== $post) {
                if ($p->getNumber() > $post->getNumber()) {
                    break;
                }
                $candidate = $p;
            }
        }

        return $candidate;
    }

    /**
     * @see PostRepositoryInterface::createNewPost
     */
    public function createNewPost()
    {
        $class = $this->getObjectClass();

        return new $class();
    }
}

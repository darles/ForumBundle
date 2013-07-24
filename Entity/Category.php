<?php

namespace Darles\Bundle\ForumBundle\Entity;

use Darles\Bundle\ForumBundle\Model\Category as BaseCategory;
use Doctrine\Common\Collections\ArrayCollection;
use Darles\Bundle\ForumBundle\Entity\Topic;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class Category extends BaseCategory
{

    protected $id;

    protected $slug;

    protected $name;

    protected $position;

    protected $topics;

    protected $numTopics;

    protected $numPosts;

    /**
     * Category constructor
     */
    public function __construct()
    {
        $this->metadata = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getTopics()
    {
        return $this->topics;
    }

    /**
     * @param Topic $topic
     */
    public function addTopics(Topic $topic)
    {
        $topic->setCategory($this);
        $this->topics->add($this);
    }


}
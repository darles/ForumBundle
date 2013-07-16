<?php

namespace Darles\Bundle\ForumBundle\Entity;

use Darles\Bundle\ForumBundle\Model\Category as BaseCategory;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Darles\Bundle\ForumBundle\Entity\Topic;

/**
 * @ORM\Entity(repositoryClass="Darles\Bundle\ForumBundle\Entity\CategoryRepository")
 */
class Category extends BaseCategory
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="slug", type="string", length=255)
     */
    protected $slug;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(name="position", type="integer")
     */
    protected $position;

    /**
     * @ORM\OneToMany(targetEntity="Darles\Bundle\ForumBundle\Entity\Topic", mappedBy="category" , cascade={"persist", "remove"})
     */
    protected $topics;

    /**
     * @ORM\Column(name="numTopics", type="integer")
     */
    protected $numTopics;

    /**
     * @ORM\Column(name="numPosts", type="integer")
     */
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
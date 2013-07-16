<?php

namespace Darles\Bundle\ForumBundle\Entity;

use Darles\Bundle\ForumBundle\Model\Topic as BaseTopic;
use Doctrine\ORM\Mapping as ORM;
use Darles\Bundle\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Darles\Bundle\ForumBundle\Entity\TopicRepository")
 */
class Topic extends BaseTopic
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Darles\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @ORM\Column(name="slug", type="string", length=255)
     */
    protected $slug;

    /**
     * @ORM\Column(name="subject", type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $subject;

    /**
     * @ORM\Column(name="numViews", type="integer")
     */
    protected $numViews;

    /**
     * @ORM\Column(name="numPosts", type="integer")
     */
    protected $numPosts;

    /**
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="pulledAt", type="datetime")
     */
    protected $pulledAt;

    /**
     * @ORM\ManyToOne(targetEntity="Darles\Bundle\ForumBundle\Entity\Category", inversedBy="topics")
     */
    protected $category;

    public function setAuthor(User $user)
    {
        $this->author = $user;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getAuthorName()
    {
        return 'anonymous';
    }

    public function getCategory()
    {
        return $this->category;
    }
}
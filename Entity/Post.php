<?php

namespace Darles\Bundle\ForumBundle\Entity;

use Darles\Bundle\ForumBundle\Model\Post as BasePost;
use Doctrine\ORM\Mapping as ORM;
use Darles\Bundle\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Darles\Bundle\ForumBundle\Entity\PostRepository")
 */
class Post extends BasePost
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Topic")
     */
    protected $topic;

    /**
     * @ORM\ManyToOne(targetEntity="Darles\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @ORM\Column(name="message", type="text")
     * @Assert\NotBlank(message="Please write a message")
     *
     * @var string
     */
    protected $message;

    /**
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected $createdAt;

    public function getAuthorName()
    {
        return 'anonymous';
    }

    public function setAuthor(User $user)
    {
        $this->author = $user;
    }

    public function getAuthor()
    {
        return $this->author;
    }
}
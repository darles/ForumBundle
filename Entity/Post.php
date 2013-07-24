<?php

namespace Darles\Bundle\ForumBundle\Entity;

use Darles\Bundle\ForumBundle\Model\Post as BasePost;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class Post extends BasePost
{

    protected $id;

    protected $topic;

    protected $message;

    protected $createdAt;

    public function getAuthorName()
    {
        return 'anonymous';
    }

}
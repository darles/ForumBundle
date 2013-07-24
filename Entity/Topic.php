<?php

namespace Darles\Bundle\ForumBundle\Entity;

use Darles\Bundle\ForumBundle\Model\Topic as BaseTopic;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class Topic extends BaseTopic
{

    protected $id;

    protected $slug;

    protected $subject;

    protected $numViews;

    protected $numPosts;

    protected $createdAt;

    protected $pulledAt;

    protected $category;

    public function getAuthorName()
    {
        return 'anonymous';
    }

    public function getCategory()
    {
        return $this->category;
    }
}
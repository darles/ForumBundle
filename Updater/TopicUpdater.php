<?php

namespace Darles\Bundle\ForumBundle\Updater;

use Darles\Bundle\ForumBundle\Model\Topic;
use Darles\Bundle\ForumBundle\Model\PostRepositoryInterface;

class TopicUpdater
{
    protected $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function update(Topic $topic)
    {
        $posts = $this->postRepository->findAllByTopic($topic, false);

        $topic->setNumPosts(count($posts));
        $topic->setFirstPost(reset($posts));
        $lastPost = end($posts);
        $topic->setLastPost($lastPost);
        $topic->setPulledAt($lastPost->getCreatedAt());

        foreach ($posts as $index => $post) {
            $post->setNumber($index + 1);
        }
    }
}

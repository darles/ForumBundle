<?php

namespace Darles\Bundle\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Darles\Bundle\ForumBundle\Model\Topic;
use Darles\Bundle\ForumBundle\Model\Post;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PostController extends Controller
{

    /**
     * @param $categorySlug
     * @param $slug
     * @return mixed
     */
    public function newAction($categorySlug, $slug)
    {
        $topic = $this->findTopicOr404($categorySlug, $slug);
        $form = $this->get('darles_forum.form.post');

        return $this->container->get('templating')->renderResponse(
            'DarlesForumBundle:Post:new.html.' . $this->getEngine(), array(
            'form' => $form->createView(),
            'topic' => $topic,
        ));
    }

    /**
     * @param $categorySlug
     * @param $slug
     * @return RedirectResponse
     */
    public function createAction($categorySlug, $slug)
    {
        $topic = $this->findTopicOr404($categorySlug, $slug);
        $form = $this->get('darles_forum.form.post');
        $post = $this->get('darles_forum.repository.post')->createNewPost();
        $post->setTopic($topic);
        $form->handleRequest($this->get('request'));

        if (!$form->isValid()) {
            return $this->container->get('templating')->renderResponse(
                'DarlesForumBundle:Post:new.html.' . $this->getEngine(), array(
                'form' => $form->createView(),
                'topic' => $topic,
            ));
        }

        $post = $form->getData();
        $post->setTopic($topic);
        $this->get('darles_forum.creator.post')->create($post);

        $objectManager = $this->get('darles_forum.object_manager');
        $objectManager->persist($post);
        $objectManager->flush();

        $this->get('session')->getFlashBag()->add('success', 'darles_forum_post_create/success');
        $url = $this->get('darles_forum.router.url_generator')->urlForPost($post);

        return new RedirectResponse($url);
    }

    /**
     * @param $id
     * @return RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteAction($id)
    {
        $post = $this->get('darles_forum.repository.post')->find($id);
        if (!$post) {
            throw new NotFoundHttpException(sprintf('No post found with id "%s"', $id));
        }

        $precedentPost = $this->get('darles_forum.repository.post')->getPostBefore($post);
        $this->get('darles_forum.remover.post')->remove($post);
        $this->get('darles_forum.object_manager')->flush();

        return new RedirectResponse($this->get('darles_forum.router.url_generator')->urlForPost($precedentPost));
    }

    /**
     * @param $categorySlug
     * @param $slug
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function findTopicOr404($categorySlug, $slug)
    {
        $category = $this
            ->get('darles_forum.repository.category')
            ->findOneBySlug($categorySlug);

        if (null === $category) {
            throw new NotFoundHttpException(sprintf(
                'The category with slug "%s" was not found.',
                $categorySlug
            ));
        }

        $topic = $this
            ->get('darles_forum.repository.topic')
            ->findOneByCategoryAndSlug($category, $slug);

        if (null === $topic) {
            throw new NotFoundHttpException(sprintf(
                'The topic with slug "%s" was not found in category with slug "%s".',
                $slug,
                $categorySlug
            ));
        }

        return $topic;
    }

    /**
     * @return mixed
     */
    protected function getEngine()
    {
        return $this->container->getParameter('darles_forum.templating.engine');
    }
}

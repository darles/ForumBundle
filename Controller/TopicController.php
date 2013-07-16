<?php

namespace Darles\Bundle\ForumBundle\Controller;

use Darles\Bundle\ForumBundle\Form\TopicForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Darles\Bundle\ForumBundle\Model\Topic;
use Darles\Bundle\ForumBundle\Model\Category;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class TopicController extends Controller
{
    /**
     * @param Category $category
     * @return mixed
     */
    public function newAction(Category $category = null)
    {
        $form = $this->get('darles_forum.form.new_topic');
        $topic = $this->get('darles_forum.repository.topic')->createNewTopic();
        if ($category) {
            $topic->setCategory($category);
        }
        $form->setData($topic);

        return $this->container->get('templating')->renderResponse(
            'DarlesForumBundle:Topic:new.html.' . $this->getEngine(), array(
            'form' => $form->createView(),
            'category' => $category
        ));
    }

    /**
     * @param Category $category
     * @return RedirectResponse
     */
    public function createAction(Category $category = null)
    {
        $form = $this->get('darles_forum.form.new_topic');
        $form->handleRequest($this->get('request'));

        $topic = $form->getData();
        if (!$form->isValid()) {
            return $this->container->get('templating')->renderResponse(
                'DarlesForumBundle:Topic:new.html.' . $this->getEngine(), array(
                'form' => $form->createView(),
                'category' => $category
            ));
        }

        $this->get('darles_forum.creator.topic')->create($topic);
        $this->get('darles_forum.creator.post')->create($topic->getFirstPost());

        $objectManager = $this->get('darles_forum.object_manager');
        $objectManager->persist($topic);
        $objectManager->persist($topic->getFirstPost());
        $objectManager->flush();

        $this->get('session')->getFlashBag()->add('notice', 'darles_forum_topic_create/success');
        $url = $this->get('darles_forum.router.url_generator')->urlForTopic($topic);

        return new RedirectResponse($url);
    }

    /**
     * @param $categorySlug
     * @param array $pagerOptions
     * @return mixed
     */
    public function listAction(Request $request, $categorySlug)
    {
        $paginator = $this->container->get('knp_paginator');
        if (null === $categorySlug) {
            $category = null;
            $topics = $this->get('darles_forum.repository.topic')->findAllWithPagination($paginator, $request->get('page', 1), $this->container->getParameter('darles_forum.paginator.posts_per_page'));
        } else {
            $category = $this->findCategoryOr404($categorySlug);
            $topics = $this->get('darles_forum.repository.topic')->findAllByCategory($category, $paginator, $request->get('page', 1), $this->container->getParameter('darles_forum.paginator.posts_per_page'));
        }


        return $this->container->get('templating')->renderResponse(
            'DarlesForumBundle:Topic:list.html.' . $this->getEngine(), array(
            'topics' => $topics,
            'category' => $category,
        ));
    }

    public function showAction(Request $request, $categorySlug, $slug)
    {
        $paginator = $this->container->get('knp_paginator');
        $topic = $this->findTopic($categorySlug, $slug);
        $this->get('darles_forum.repository.topic')->incrementTopicNumViews($topic);

        $posts = $this->get('darles_forum.repository.post')->findAllByTopic($topic, $paginator, $request->get('page', 1), $this->container->getParameter('darles_forum.paginator.posts_per_page'));

        return $this->container->get('templating')->renderResponse(
            'DarlesForumBundle:Topic:show.html.' . $this->getEngine(), array(
            'topic' => $topic,
            'posts' => $posts
        ));
    }

    public function postNewAction($categorySlug, $slug)
    {
        return $this->forward('DarlesForumBundle:Post:new', array(
            'categorySlug' => $categorySlug,
            'slug' => $slug
        ));
    }

    public function postCreateAction($categorySlug, $slug)
    {
        return $this->forward('DarlesForumBundle:Post:create', array(
            'categorySlug' => $categorySlug,
            'slug' => $slug
        ));
    }

    public function deleteAction($id)
    {
        $topic = $this->get('darles_forum.repository.topic')->find($id);
        if (!$topic) {
            throw new NotFoundHttpException(sprintf('No topic found with id "%s"', $id));
        }

        $this->get('darles_forum.remover.topic')->remove($topic);
        $this->get('darles_forum.object_manager')->flush();

        return new RedirectResponse($this->get('darles_forum.router.url_generator')->urlForCategory($topic->getCategory()));
    }

    protected function getRenderer()
    {
        return $this->container->getParameter('darles_forum.templating.engine');
    }

    /**
     * Find a topic by its category slug and topic slug
     *
     * @return Topic
     **/
    public function findTopic($categorySlug, $topicSlug)
    {
        $category = $this->get('darles_forum.repository.category')->findOneBySlug($categorySlug);
        if (!$category) {
            throw new NotFoundHttpException(sprintf('The category with slug "%s" does not exist', $categorySlug));
        }
        $topic = $this->get('darles_forum.repository.topic')->findOneByCategoryAndSlug($category, $topicSlug);
        if (!$topic) {
            throw new NotFoundHttpException(sprintf('The topic with slug "%s" does not exist', $topicSlug));
        }

        return $topic;
    }

    /**
     * Finds the category having the specified slug or throws a 404 exception
     *
     * @param  string $slug
     *
     * @return Category
     * @throws NotFoundHttpException
     */
    protected function findCategoryOr404($slug)
    {
        $category = $this
            ->get('darles_forum.repository.category')
            ->findOneBySlug($slug);

        if (!$category) {
            throw new NotFoundHttpException(sprintf(
                'The category with slug "%s" was not found.',
                $slug
            ));
        }

        return $category;
    }

    protected function getEngine()
    {
        return $this->container->getParameter('darles_forum.templating.engine');
    }
}

<?php

namespace Darles\Bundle\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    /**
     * @return mixed
     */
    public function listAction(Request $request)
    {
        $paginator = $this->container->get('knp_paginator');

        $categories = $this->get('darles_forum.repository.category')->findAllWithPagination($paginator, $request->get('page', 1), $this->container->getParameter('darles_forum.paginator.topics_per_page'));
        return $this->container->get('templating')->renderResponse(
            'DarlesForumBundle:Category:list.html.' . $this->getEngine(), array(
            'categories' => $categories
        ));
    }

    /**
     * @param $slug
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction(Request $request, $slug)
    {
        $paginator = $this->container->get('knp_paginator');
        $category = $this->get('darles_forum.repository.category')->findOneBy(array('slug' => $slug));

        if (!$category) {
            throw new NotFoundHttpException(sprintf('The category %s does not exist.', $slug));
        }

        $posts = $this->get('darles_forum.repository.post')->findRecentPosts($paginator, $request->get('page', 1), $this->container->getParameter('darles_forum.paginator.topics_per_page'));

        $form = $this->get('darles_forum.form.new_topic');
        $topic = $this->get('darles_forum.repository.topic')->createNewTopic();

        if ($category) {
            $topic->setCategory($category);
        }

        $form->setData($topic);

        return $this->container->get('templating')->renderResponse(
            'DarlesForumBundle:Category:show.html.' . $this->getEngine(), array(
            'category' => $category,
            'posts' => $posts,
            'form' => $form->createView(),
            'pagination' => 1
        ));
    }

    /**
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function topicNewAction($slug)
    {
        $category = $this->get('darles_forum.repository.category')->findOneBySlug($slug);

        if (!$category) {
            throw new NotFoundHttpException(sprintf('The category "%s" does not exist.', $slug));
        }

        return $this->forward('DarlesForumBundle:Topic:new', array('category' => $category));
    }

    /**
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function topicCreateAction($slug)
    {
        $category = $this->get('darles_forum.repository.category')->findOneBySlug($slug);

        if (!$category) {
            throw new NotFoundHttpException(sprintf('The category "%s" does not exist.', $slug));
        }

        return $this->forward('DarlesForumBundle:Topic:create', array('category' => $category));
    }

    protected function getEngine()
    {
        return $this->container->getParameter('darles_forum.templating.engine');
    }

}

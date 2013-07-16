<?php

namespace Darles\Bundle\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends Controller
{
    /**
     * @return mixed
     */
    public function listAction()
    {
        $categories = $this->get('darles_forum.repository.category')->findAll();

        $template = sprintf('%s:list.html.%s', $this->container->getParameter('darles_forum.templating.location.category'), $this->getRenderer());
        return $this->get('templating')->renderResponse($template, array('categories' => $categories));
    }

    /**
     * @param $slug
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction($slug)
    {
        $topicsLimit = $this->container->getParameter('darles_forum.topics_per_page');
        $category = $this->get('darles_forum.repository.category')->findOneBy(array('slug' => $slug));
        $paginator = $this->container->get('knp_paginator');

        if (!$category) {
            throw new NotFoundHttpException(sprintf('The category %s does not exist.', $slug));
        }

        $form = $this->get('darles_forum.form.new_topic');
        $topic = $this->get('darles_forum.repository.topic')->createNewTopic();

        if ($category) {
            $topic->setCategory($category);
        }

        $form->setData($topic);

        $template = sprintf('%s:show.%s.%s', $this->container->getParameter('darles_forum.templating.location.category'), $this->get('request')->getRequestFormat(), $this->getRenderer());
        return $this->get('templating')->renderResponse($template, array(
            'category' => $category,
            'posts'   => $posts,
            'form'   => $form->createView(),
            'page' => 1
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

    /**
     * @return mixed
     */
    protected function getRenderer()
    {
        return $this->container->getParameter('darles_forum.templating.engine');
    }
}

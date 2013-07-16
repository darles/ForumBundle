<?php

namespace Darles\Bundle\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Darles\Bundle\ForumBundle\Form\SearchFormType;
use Darles\Bundle\ForumBundle\Search\Search;
use Symfony\Component\HttpFoundation\Request;

class ForumController extends Controller
{
    public function indexAction(Request $request)
    {
        $paginator = $this->container->get('knp_paginator');
        $categories = $this->get('darles_forum.repository.category')->findAllWithPagination($paginator, $request->get('page', 1), $this->container->getParameter('darles_forum.paginator.categories_per_page'));

        return $this->container->get('templating')->renderResponse(
            'DarlesForumBundle:Forum:index.html.' . $this->getEngine(), array(
            'categories' => $categories
        ));
    }

    public function searchAction()
    {
        $search = new Search();
        $form = $this->get('form.factory')->create(new SearchFormType(), $search);
        $form->bind(array('query' => $this->get('request')->query->get('q')));
        $query = $form->getData()->getQuery();

        $results = null;
        if ($form->isValid()) {
            $page = $this->get('request')->query->get('page', 1);
            $results = $this->get('darles_forum.repository.post')->search($query, true);
            $results->setCurrentPage($page);
            $results->setMaxPerPage();
        }

        return $this->container->get('templating')->renderResponse(
            'DarlesForumBundle:Forum:search.html.' . $this->getEngine(), array(
            'form' => $form->createView(),
            'results' => $results,
            'query' => $query
        ));
    }

    protected function getEngine()
    {
        return $this->container->getParameter('darles_forum.templating.engine');
    }
}

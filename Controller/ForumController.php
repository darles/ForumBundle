<?php

namespace Darles\Bundle\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Darles\Bundle\ForumBundle\Form\SearchFormType;
use Darles\Bundle\ForumBundle\Search\Search;

class ForumController extends Controller
{
    public function indexAction()
    {
        $categories = $this->get('darles_forum.repository.category')->findAll();
        $template = sprintf('%s:index.html.%s', $this->container->getParameter('darles_forum.templating.location.forum'), $this->getRenderer());
        return $this->get('templating')->renderResponse($template, array(
            'categories' => $categories,
            'page' => $this->get('request')->query->get('page', 1)
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
            $results->setMaxPerPage($this->container->getParameter('darles_forum.paginator.search_results_per_page'));
        }

        $template = sprintf('%s:search.html.%s', $this->container->getParameter('darles_forum.templating.location.forum'), $this->getRenderer());
        return $this->get('templating')->renderResponse($template, array(
            'form' => $form->createView(),
            'results' => $results,
            'query' => $query
        ));
    }

    protected function getRenderer()
    {
        return $this->container->getParameter('darles_forum.templating.engine');
    }
}

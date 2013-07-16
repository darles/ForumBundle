<?php

namespace Darles\Bundle\ForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('query', 'text');
    }

    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'Darles\Bundle\ForumBundle\Search\Search',
            'csrf_protection' => false,
        );
    }

    public function getName()
    {
        return 'Search';
    }
}

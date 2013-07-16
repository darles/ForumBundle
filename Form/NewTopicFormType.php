<?php

namespace Darles\Bundle\ForumBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Darles\Bundle\ForumBundle\Model\CategoryRepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Darles\Bundle\ForumBundle\Form\PostFormType;

class NewTopicFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('subject');
        $builder->add('firstPost', new PostFormType(), array('data_class' => 'Darles\Bundle\ForumBundle\Entity\Post'));
        $builder->add('category', 'entity', array('class' => 'Darles\Bundle\ForumBundle\Entity\Category'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Darles\Bundle\ForumBundle\Entity\Topic',
        ));
    }

    public function getName()
    {
        return 'NewTopic';
    }
}

<?php

namespace CPASimUSante\SimupollBundle\Form;

use CPASimUSante\SimupollBundle\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuestionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                    'label' => 'question_title',
                    'required' => true
                )
            )
            ->add('category', 'entity', array(
                    'class' => 'CPASimUSante\\SimupollBundle\\Entity\\Category',
                    'label' => 'Category.value',
                    'required' => true,
                    'empty_value' => 'choose_category',
                    'choice_label' => 'indentedName',   //the formated name
                    'query_builder' => function (CategoryRepository $cr)  {
                        //https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/tree.md#repository-methods-all-strategies
                        //node (null = all nodes), direct (true or null), sortByField, direction, includeNode (true/false)
                        return $cr->getChildrenQueryBuilder(null, null, 'root', 'asc', false);
                    }
                )
            )
            ->add(
                'propositions', 'collection', array(
                    'type'          => new PropositionType(),
                    'by_reference'  => false,
                    'prototype'     => true,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                )
            )
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CPASimUSante\SimupollBundle\Entity\Question',
            'translation_domain' => 'resource',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cpasimusante_simupollbundle_question';
    }
}

<?php

namespace CPASimUSante\SimupollBundle\Form;

use CPASimUSante\SimupollBundle\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

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
                    'label' => 'category_choice',
                    'required' => true,
                    'empty_value' => 'category_choice',
                    'choice_label' => 'indentedName',   //the formated name
                    'query_builder' => function (NestedTreeRepository $cr)  {
                        //https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/tree.md#repository-methods-all-strategies
                        //node (null = all nodes), direct (true or null), sortByField, direction, includeNode (true/false)
                        return $cr->getChildrenQueryBuilder(null, null, 'lft', 'asc', false);
                    },
                )
            )
            ->add(
                'propositions', 'collection', array(
                    'type'              => new PropositionType(),
                    'label'             => 'proposition_value',
                    'by_reference'      => false,
                    'prototype'         => true,
                    'prototype_name'    => '__proposition_proto__',
                    'allow_add'         => true,
                    'allow_delete'      => true,
                )
            )
            ->add('orderq', 'hidden', array(
                    'data'              => 0    //to avoid null
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

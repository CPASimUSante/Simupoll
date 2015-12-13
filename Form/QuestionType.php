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
/*
                    'query_builder' => function (CategoryRepository $cr) use ($uid) {
                        if ($this->catID === -1) {
                            return $cr->getUserCategory($uid);
                        } else {
                            return $cr->createQueryBuilder('c')
                                ->where('c.id = ?1')
                                ->setParameter(1, $this->catID);
                        }
                    }
*/
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

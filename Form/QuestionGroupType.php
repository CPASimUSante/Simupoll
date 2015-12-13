<?php

namespace CPASimUSante\SimupollBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuestionGroupType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                    'label' => 'group_name',
                    'required' => true
                )
            )
            ->add(
                'dateStart', 'datetime', array(
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'format' => 'dd/MM/yyyy H:mm:ss',
                    'attr' => array('data-format' => 'dd/MM/yyyy H:mm:ss'),
                    'label' => 'date_start',
                    'required' => false
                )
            )
            ->add(
                'dateEnd', 'datetime', array(
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'format' => 'dd/MM/yyyy H:mm:ss',
                    'attr' => array('data-format' => 'dd/MM/yyyy H:mm:ss'),
                    'label' => 'date_end',
                    'required' => false
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
            'data_class' => 'CPASimUSante\SimupollBundle\Entity\QuestionGroup',
            'translation_domain' => 'resource',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cpasimusante_simupollbundle_questiongroup';
    }
}

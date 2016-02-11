<?php

namespace CPASimUSante\SimupollBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class StatmanageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('userList', 'text', array(
                'label' => 'userlist',
                'required' => true,
                'constraints' => new NotBlank()
            )
        );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CPASimUSante\SimupollBundle\Entity\Statmanage',
            'translation_domain' => 'resource',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cpasimusante_simupollbundle_statmanage';
    }
}

<?php

namespace CPASimUSante\SimupollBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PeriodType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dateParams = array(
            'format' => 'dd-MM-yyyy',
            'widget' => 'single_text',
            'input' => 'datetime',
            'required' => true,
            'component' => true,
            'attr' => array(
                'class' => 'datepicker input-small',
                'data-date-format' => 'dd-mm-yyyy',
                'autocomplete' => 'off'
            ),
            'constraints' => array(new NotBlank())
        );
        $startParam = $dateParams;
        $startParam['label'] = 'period_start';
        $stopParam = $dateParams;
        $stopParam['label'] = 'period_stop';

       /* $generalOptions = array(
            'required' => true,
            'read_only' => true,
            'component' => true,
            'autoclose' => true,
            'input' => 'datetime',
            'language' => $options['language'],
            'format' => 'yyyy-MM-dd H:m:s',
            'mapped' => false,
        );
        $startOptions = $generalOptions;
        $startOptions['label'] = 'period_start';*/
        $builder
            ->add('title', 'text', array(
                    'label' => 'period_title',
                    'required' => false
                )
            )
            ->add('start', 'datepicker', $startParam)
            ->add('stop', 'datepicker', $stopParam)
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CPASimUSante\SimupollBundle\Entity\Period',
            'language' => 'en',
            'translation_domain' => 'resource'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cpasimusante_simupollbundle_period';
    }
}

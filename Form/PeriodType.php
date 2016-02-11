<?php

namespace CPASimUSante\SimupollBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Translation\TranslatorInterface;

class PeriodType extends AbstractType
{


    private function setFieldOptions($options, $label)
    {
        return array(
            'label' => $label,
            'required' => true,
            'read_only' => false,
            'component' => true,
            'autoclose' => true,
            'input' => 'datetime',
            'language' => $options['language'],
            'format' => $this->translator->trans('date_form_format', array(), 'platform'),
            'mapped' => false,
        );
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $generalOptions = array(
            'required' => true,
            'read_only' => false,
            'component' => true,
            'autoclose' => true,
            'input' => 'datetime',
            'language' => $options['language'],
            'format' => $this->translator->trans('date_form_format', array(), 'platform'),
            'mapped' => false,
        );
        $startOptions = $generalOptions;
        $startOptions['label'] = 'period_start';
        $builder
            ->add('start', 'datepicker', $this->setFieldOptions($options, 'period_start'))
            ->add('stop', 'datepicker', $this->setFieldOptions($options, 'period_stop'))
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

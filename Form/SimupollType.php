<?php
namespace CPASimUSante\SimupollBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimupollType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name', 'hidden', array(
                    'data' => 'simupoll'
                )
            );
        //To avoid displaying them in Simupoll Resource creation modal
        if ($options['inside']) {
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CPASimUSante\SimupollBundle\Entity\Simupoll',
            'translation_domain' => 'resource',
            'inside' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cpasimusante_simupoll';
    }
}
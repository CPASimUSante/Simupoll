<?php
namespace CPASimUSante\SimupollBundle\Form;

use CPASimUSante\SimupollBundle\Entity\Simupoll;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use CPASimUSante\SimupollBundle\Repository\CategoryRepository;

class SimupollType extends AbstractType
{
    /**
     * @var int the simupoll
     */
    private $simupoll;

    public function __construct($simupoll = 0)
    {
        $this->simupoll = $simupoll;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $simupoll    = $this->simupoll;

        $builder
            ->add(
                'name', 'hidden', array(
                    'data' => 'simupoll'
                )
            )
            ->add(
                'title', 'text', array(
                    'label' => 'title'
                )
            );

        //To avoid displaying those fields in Simupoll resource creation modal
        if ($options['inside']) {
            $builder
                ->add('description', 'tinymce', array(
                    'theme_options' => array('label_width' => 'col-md-1', 'control_width' => 'col-md-11'),
                    'attr' => array(
                          'id' => 'cpasimusante_simupoll_description'
                        )
                    ))
                ->add(
                    'questions', 'collection', array(
                        'type'              => new QuestionType($simupoll),
                        'by_reference'      => false,
                        'prototype'         => true,
                        'prototype_name'    => '__question_proto__',
                        'allow_add'         => true,
                        'allow_delete'      => true,
                    )
                );
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

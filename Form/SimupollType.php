<?php
namespace CPASimUSante\SimupollBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use CPASimUSante\SimupollBundle\Repository\CategoryRepository;

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
            )
            ->add(
                'title', 'text', array(
                    'label' => 'title'
                )
            );
        //To avoid displaying them in Simupoll Resource creation modal
        if ($options['inside']) {
           /* $builder
                ->add(
                    'category', 'entity', [
                        'label'         => 'Categorie',
                        'class'         => 'CPASimUSanteSimupollBundle:Category',
                        'choice_label'  => 'name',
                        'empty_value'   => 'Choisissez une catégorie',
                        'query_builder' => function(CategoryRepository $er) {
                            $qb = $er->createQueryBuilder('c')
                                ->orderBy('c.name', 'ASC');
                            return $qb;
                        }
                    ]
                );*/
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
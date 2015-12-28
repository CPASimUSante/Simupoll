<?php

namespace CPASimUSante\SimupollBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CPASimUSante\SimupollBundle\Repository\TagRepository;

class TagType extends AbstractType
{
    /**
     * @var int the resource type ( Exercise)
     */
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user   = $this->user;

        $builder
            ->add('name', 'text', array(
                'label' => 'Nom'
            ))
            ->add('parent', 'entity', array(
                'class' => 'CPASimUSanteSimupollBundle:Tag',
                'choice_label' => 'name',
                'label' => 'Parent',
                'query_builder' => function(TagRepository $er) use ($user) {
                    $qb = $er->createQueryBuilder('tag')
                        ->where('tag.user = :user')
                        ->setParameter('user', $user);
                    return $qb;
                }
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CPASimUSante\SimupollBundle\Entity\Tag'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cpasimusante_simupollbundle_tag';
    }
}

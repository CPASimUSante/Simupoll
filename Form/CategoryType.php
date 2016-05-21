<?php

namespace CPASimUSante\SimupollBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Entity\Category;

class CategoryType extends AbstractType
{
    /**
     * @var int the simupoll
     */
    private $simupoll;
    private $category;

    public function __construct(Simupoll $simupoll = null, Category $category = null)
    {
        $this->simupoll = $simupoll;
        $this->category = $category;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $simupoll = $this->simupoll;
        $category = $this->category;

        $builder->add('name', 'text', array(
                'label' => 'category_name',
                'required' => true,
                'constraints' => new NotBlank(),
            ));
        //for category modification
        if ($options['inside']) {
            $builder->add('parent', 'entity', array(
                    'class' => 'CPASimUSante\\SimupollBundle\\Entity\\Category',
                    'label' => 'category_choice',
                    'required' => true,
                    'choice_label' => 'indentedName',   //the formated name
                    'query_builder' => function (\CPASimUSante\SimupollBundle\Repository\CategoryRepository $cr) use ($simupoll, $category) {
                        return $cr->getCategoriesWithoutChildren($simupoll, $category);
                    },
                ));
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CPASimUSante\SimupollBundle\Entity\Category',
            'translation_domain' => 'resource',
            'inside' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cpasimusante_simupollbundle_category';
    }
}

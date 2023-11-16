<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryGroupType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'expanded' => true,
            'searchable' => true,
            'class' => Category::class,
            'choice_label' => 'title',
            'multiple' => true,
            'label' => 'categories',
            'query_builder' => function (EntityRepository $er): QueryBuilder {
                $qb = $er->createQueryBuilder('category');
                return $qb;
            },
            'choice_translation_domain' => true,
        ]);
        $resolver->setAllowedTypes('searchable', 'bool');
    }

    public function getParent(): string
    {
        return EntityType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'category_selection_group';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (isset($options['searchable'])) {
            $view->vars['searchable'] = $options['searchable'];
        }
    }
}

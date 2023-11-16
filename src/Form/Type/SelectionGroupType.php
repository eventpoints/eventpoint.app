<?php

declare(strict_types=1);

namespace App\Form\Type;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectionGroupType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'searchable' => true,
            'multiple' => true,
            'expanded' => true,
            'choice_attr' => fn ($choice, $key, $value) => [
                'class' => 'custom-checkbox',
            ],
        ]);
        $resolver->setAllowedTypes('searchable', Types::BOOLEAN);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'selection_group';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (isset($options['searchable'])) {
            $view->vars['searchable'] = $options['searchable'];
        }
    }
}

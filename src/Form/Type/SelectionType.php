<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectionType extends AbstractType
{
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'expanded'    => true,
            'multiple'    => false,
            'cols'        => 2,
            'placeholder' => false,
        ]);

        $resolver->setAllowedTypes('cols', 'int');
        $resolver->setAllowedValues('cols', [1, 2, 3, 4]);
    }

    #[\Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['cols'] = $options['cols'];
    }

    #[\Override]
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'selection_type';
    }
}

<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EntitySelectionGroupType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'expanded' => true,
            'multiple' => true,
            'searchable' => true,
            'empty_message' => $this->translator->trans('no-select-options'),
        ]);
        $resolver->setAllowedTypes('searchable', 'bool');
    }

    public function getParent(): string
    {
        return EntityType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'entity_selection_group';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (isset($options['searchable'])) {
            $view->vars['searchable'] = $options['searchable'];
        }

        if (isset($options['empty_message'])) {
            $view->vars['empty_message'] = $options['empty_message'];
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Form\Form\Poll;

use App\Entity\Poll\Poll;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class PollFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prompt', TextType::class, [
                'label' => false,
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'attr' => [
                    'placeholder' => 'What would you like to ask?',
                ],
            ])
            ->add('pollOptions', LiveCollectionType::class, [
                'label' => false,
                'entry_type' => PollOptionFormType::class,
                'allow_add' => true,
                'delete_empty' => true,
                'prototype' => true,
                'by_reference' => false,
                'button_delete_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'btn btn-danger m-0 bi bi-x-lg',
                    ],
                ],
                'button_add_options' => [
                    'label' => 'add-poll-option',
                    'attr' => [
                        'class' => 'btn btn-secondary w-100',
                    ],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Poll::class,
        ]);
    }
}

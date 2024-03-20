<?php

declare(strict_types=1);

namespace App\Form\Form\Event;

use App\Entity\Event\EventOrganiser;
use App\Entity\Event\EventRole;
use App\Entity\User\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventOrganiserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName',
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'multiple' => false,
            ])
            ->add('roles', EntityType::class, [
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'class' => EventRole::class,
                'choice_label' => 'name',
                'translation_domain' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventOrganiser::class,
        ]);
    }
}

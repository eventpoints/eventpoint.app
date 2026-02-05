<?php

declare(strict_types=1);

namespace App\Form\Form\Event;

use App\Entity\Event\EventOrganiserInvitation;
use App\Entity\User\User;
use App\Enum\EventParticipantRoleEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventOrganiserInviationFormType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod(Request::METHOD_GET)
            ->add('owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName',
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'attr' => [
                    'data-action' => 'keyup->user-autocomplete#typing',
                ],
                'multiple' => false,
                'autocomplete' => true,
            ])
            ->add('role', EnumType::class, [
                'label' => 'role',
                'class' => EventParticipantRoleEnum::class,
                'choice_label' => fn (EventParticipantRoleEnum $role) => $role->value,
                'choice_translation_domain' => true,
            ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventOrganiserInvitation::class,
        ]);
    }
}

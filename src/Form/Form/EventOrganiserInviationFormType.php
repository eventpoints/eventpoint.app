<?php

declare(strict_types=1);

namespace App\Form\Form;

use App\Entity\Event\EventRole;
use App\Entity\EventOrganiserInvitation;
use App\Form\Type\EntitySelectionGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventOrganiserInviationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod(Request::METHOD_GET)
            ->add('email', EmailType::class, [
                'mapped' => false,
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'attr' => [
                    'data-action' => 'keyup->user-autocomplete#typing',
                ],
            ])->add('roles', EntitySelectionGroupType::class, [
                'label' => 'roles',
                'expanded' => true,
                'searchable' => false,
                'class' => EventRole::class,
                'choice_label' => 'title',
                'multiple' => true,
                'choice_translation_domain' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventOrganiserInvitation::class,
        ]);
    }
}

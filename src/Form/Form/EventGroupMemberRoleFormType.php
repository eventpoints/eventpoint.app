<?php

declare(strict_types=1);

namespace App\Form\Form;

use App\Entity\EventGroup\EventGroupMember;
use App\Entity\EventGroup\EventGroupRole;
use App\Entity\User;
use App\Enum\EventGroupRoleEnum;
use App\Form\Type\EntitySelectionGroupType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventGroupMemberRoleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName',
                'disabled' => true,
            ])
            ->add('roles', EntitySelectionGroupType::class, [
                'label' => false,
                'expanded' => true,
                'multiple' => true,
                'searchable' => false,
                'class' => EventGroupRole::class,
                'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('event_group_role')
                    ->andWhere('event_group_role.title NOT IN (:titles)')
                    ->setParameter('titles', [EventGroupRoleEnum::ROLE_GROUP_CREATOR, EventGroupRoleEnum::ROLE_GROUP_MAINTAINER]),
                'choice_label' => 'title.value',
                'choice_translation_domain' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventGroupMember::class,
            'owner' => User::class,
        ]);
    }
}

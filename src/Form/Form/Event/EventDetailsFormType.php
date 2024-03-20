<?php

namespace App\Form\Form\Event;

use App\DataTransferObject\Event\EventDetailsFormDto;
use App\Entity\EventGroup\EventGroup;
use App\Entity\User\User;
use App\Form\Type\CustomCheckBoxType;
use App\Form\Type\EntitySelectionGroupType;
use App\Repository\Event\EventGroupRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventDetailsFormType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var EventDetailsFormDto $eventDetailsFormDto */
        $eventDetailsFormDto = $builder->getData();

        $builder
            ->add('title', TextType::class)
            ->add('image', FileType::class, [
                'required' => empty($eventDetailsFormDto->getBase64image()),
                'mapped' => false,
                'attr' => [
                    'data-action' => 'change->image-form-display#load',
                ],
            ])
            ->add('description', TextareaType::class)
            ->add('startAt', DateTimeType::class, [
                'html5' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'row_attr' => [
                    'data-controller' => 'calendar',
                    'data-calendar-theme-value' => '',
                ],
                'attr' => [
                    'autocomplete' => 'off',
                    'data-calendar-target' => 'dateInput',
                ],
            ])
            ->add('endAt', DateTimeType::class, [
                'html5' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'row_attr' => [
                    'data-controller' => 'calendar',
                    'data-calendar-theme-value' => '',
                ],
                'attr' => [
                    'autocomplete' => 'off',
                    'data-calendar-target' => 'dateInput',
                ],
            ])
            ->add('isPrivate', CustomCheckBoxType::class, [
                'label' => $this->translator->trans('is-event-private'),
                'required' => false,
            ]);

        $currentUser = $this->security->getUser();
        if ($currentUser instanceof User) {
            $builder->add('eventGroup', EntitySelectionGroupType::class, [
                'required' => false,
                'searchable' => false,
                'expanded' => true,
                'multiple' => false,
                'label' => $this->translator->trans('add-event-group'),
                'empty_message' => $this->translator->trans('no-groups-found'),
                'class' => EventGroup::class,
                'choice_label' => 'name',
                'empty_data' => null,
                'query_builder' => function (EventGroupRepository $er) use ($currentUser): QueryBuilder {
                    $qb = $er->createQueryBuilder('event_group');
                    $qb->andWhere(
                        $qb->expr()->eq('event_group.owner', ':owner')
                    )->setParameter('owner', $currentUser->getId());

                    $qb->andWhere(
                        $qb->expr()->eq('event_group.owner', ':owner')
                    )->setParameter('owner', $currentUser->getId());

                    return $qb;
                },
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventDetailsFormDto::class,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Form\Form\Event;

use App\DataTransferObject\Event\EventDto;
use App\Entity\Animal;
use App\Entity\Event\Category;
use App\Entity\Event\Event;
use App\Entity\EventGroup\EventGroup;
use App\Entity\User\User;
use App\Form\Type\CustomCheckBoxType;
use App\Form\Type\EntitySelectionGroupType;
use App\Form\Type\FlowbiteDateTimeType;
use App\Repository\Event\EventGroupRepository;
use DateTimeZone;
use Doctrine\ORM\QueryBuilder;
use Kerrialnewham\Autocomplete\Form\Type\AutocompleteType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventFormType extends AbstractType
{
    public function __construct(
            private readonly TranslatorInterface $translator,
            private readonly Security            $security,
    )
    {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $this->security->getUser();
        $builder
                ->add('latitude', HiddenType::class, [
                        'label' => $this->translator->trans('latitude'),
                        'attr' => [
                                'data-location-target' => 'latitude',
                        ],
                ])->add('longitude', HiddenType::class, [
                        'label' => $this->translator->trans('longitude'),
                        'attr' => [
                                'data-location-target' => 'longitude',
                        ],
                ])->add('address', HiddenType::class, [
                        'attr' => [
                                'data-location-target' => 'address',
                        ],
                ])
                ->add('title', TextType::class)
                ->add('description', TextareaType::class)
                ->add('startAt', FlowbiteDateTimeType::class, [
                        'label' => $this->translator->trans('startAt')
                ])
                ->add('endAt', FlowbiteDateTimeType::class, [
                        'label' => $this->translator->trans('endAt')
                ])
                ->add('categories', EntityType::class, [
                        'label' => false,
                        'multiple' => true,
                        'class' => Category::class,
                        'choice_label' => 'title',
                        'autocomplete' => true,
                        'placeholder' => 'categories',
                        'limit' => 10,
                        'required' => false,
                        'theme' => 'default',
                ])
                ->add('isPrivate', CustomCheckBoxType::class, [
                        'label' => $this->translator->trans('is-event-private'),
                        'required' => false,
                ]);

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
        } else {
            $builder
                    ->add('email', EmailType::class)
                    ->add('firstName', TextType::class)
                    ->add('lastName', TextType::class);
        }
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                'data_class' => EventDto::class,
                'event' => null,
        ]);
    }
}

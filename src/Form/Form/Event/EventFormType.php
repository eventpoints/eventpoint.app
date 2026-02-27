<?php

declare(strict_types=1);

namespace App\Form\Form\Event;

use App\DataTransferObject\Event\EventDto;
use App\DataTransferObject\MapLocationDto;
use App\Entity\Event\Category;
use App\Entity\User\User;
use App\Form\Type\CustomCheckBoxType;
use App\Form\Type\FlowbiteDateTimeType;
use App\Form\Type\MapLocationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Map\Map;

class EventFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly Security $security,
    ) {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $this->security->getUser();
        /** @var EventDto|null $event */
        $event = $options['data'] ?? null;

        $builder
            ->add('location', MapLocationType::class, [
                'mapped' => false,
                'data' => MapLocationDto::getFromEventDto($event),
                'map' => $options['map'],
                'height' => '320px',
                'help' => $this->translator->trans('event-location-help'),
                'event' => $event,
                'constraints' => [
                    new Assert\Callback(function ($mapLocationDto, $context): void {
                        if (! $mapLocationDto instanceof MapLocationDto) {
                            $context->buildViolation('location.required')
                                ->addViolation();
                            return;
                        }
                        if ($mapLocationDto->getLatitude() === null || $mapLocationDto->getLongitude() === null) {
                            $context->buildViolation('location.required')
                                ->addViolation();
                        }
                    }),
                ],
            ])
            ->add('title', TextType::class, [
                'label' => 'title',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'description',
            ])
            ->add('startAt', FlowbiteDateTimeType::class, [
                'label' => $this->translator->trans('startAt'),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThanOrEqual([
                        'value' => 'now',
                        'message' => 'event.start_date_must_be_in_future',
                    ]),
                ],
            ])
            ->add('endAt', FlowbiteDateTimeType::class, [
                'label' => $this->translator->trans('endAt'),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThan([
                        'propertyPath' => 'parent.all[startAt].data',
                        'message' => 'event.end_date_must_be_after_start_date',
                    ]),
                ],
            ])
            ->add('categories', EntityType::class, [
                'label' => $this->translator->trans(id: 'categories', domain: 'messages'),
                'attr' => [
                    'placeholder' => $this->translator->trans(id: 'event-categories-placeholder', domain: 'messages'),
                ],
                'multiple' => true,
                'class' => Category::class,
                'choice_label' => 'title',
                'autocomplete' => true,
                'translation_domain' => 'categories',
                'limit' => 30,
                'required' => false,
                'theme' => 'flowbite',
            ])
            ->add('isPrivate', CustomCheckBoxType::class, [
                'label' => $this->translator->trans('is-event-private'),
                'required' => false,
            ]);

        if (! $currentUser instanceof User) {
            $builder
                ->add('email', EmailType::class, [
                    'label' => 'email',
                ])
                ->add('firstName', TextType::class, [
                    'label' => 'first-name',
                ])
                ->add('lastName', TextType::class, [
                    'label' => 'last-name',
                ]);
        }
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventDto::class,
            'event' => null,
            'map' => Map::class,
            'is_edit' => false,
        ]);
    }
}

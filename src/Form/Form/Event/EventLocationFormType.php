<?php

namespace App\Form\Form\Event;

use App\DataTransferObject\Event\EventLocationFormDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventLocationFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', TextType::class, [
                'attr' => [
                    'data-location-target' => 'address',
                ],
            ])
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventLocationFormDto::class,
        ]);
    }
}

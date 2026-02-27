<?php

declare(strict_types=1);

namespace App\Form\Filter;

use App\Form\Type\SelectionType;
use App\Model\RegionalConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegionFilterType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly RegionalConfiguration $regionalSetting,
    ) {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod('get');
        $builder->add('locale', SelectionType::class, [
            'label' => $this->translator->trans('language'),
            'required' => true,
            'choices' => [
                'English' => 'en',
                'Čeština' => 'cs',
                'Русский' => 'ru',
            ],
            'data' => $this->regionalSetting->getLocale(),
        ])
            ->add('region', SelectionType::class, [
                'label' => $this->translator->trans('region-country'),
                'required' => true,
                'choices' => [
                    'Česká republika' => 'cz',
                ],
                'empty_data' => $this->regionalSetting->getRegion(),
            ])
            ->add('currency', SelectionType::class, [
                'label' => $this->translator->trans('currency'),
                'required' => true,
                'choices' => [
                    'EUR' => 'EUR',
                    'CZK' => 'CZK',
                ],
                'empty_data' => $this->regionalSetting->getLocale(),
            ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['request']);
        $resolver->setDefaults([
            'data_class' => RegionalConfiguration::class,
        ]);
    }
}

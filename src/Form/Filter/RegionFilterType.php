<?php

declare(strict_types=1);

namespace App\Form\Filter;

use App\Model\RegionalConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
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

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod('get');
        $builder->add('locale', LanguageType::class, [
            'label' => $this->translator->trans('language'),
            'choice_loader' => null,
            'choices' => [
                'English' => 'en',
                'Čeština' => 'cz',
                'Русский' => 'ru',
            ],
            'data' => $this->regionalSetting->getLocale(),
        ])
            ->add('region', CountryType::class, [
                'label' => $this->translator->trans('region-country'),
                'data' => $this->regionalSetting->getRegion(),
            ])
            ->add('currency', CurrencyType::class, [
                'choice_loader' => null,
                'choices' => [
                    'EUR' => 'EUR',
                    'CZK' => 'CZK',
                    'USD' => 'USD',
                ],
                'label' => $this->translator->trans('currency'),
                'data' => $this->regionalSetting->getCurrency(),
            ])
            ->add('timezone', TimezoneType::class, [
                'data' => $this->regionalSetting->getTimezone(),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['request']);
        $resolver->setDefaults([
            'data_class' => RegionalConfiguration::class,
        ]);
    }
}

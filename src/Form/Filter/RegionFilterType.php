<?php

declare(strict_types=1);

namespace App\Form\Filter;

use App\Entity\User;
use App\Service\RegionalSettingsService\RegionalSettingsService;
use App\ValueObject\RegionalSettingValueObject;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegionFilterType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface     $translator,
        private readonly RegionalSettingsService $regionalSettingsService,
        private readonly Security                $security,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod('get');
        /** @var User $user */
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $builder->add('locale', LanguageType::class, [
                'label' => $this->translator->trans('language'),
                'choice_loader' => null,
                'choices' => [
                    'English' => 'en',
                    'Čeština' => 'cz',
                    'Русский' => 'ru',
                ],
                'data' => $user->getLocale(),
            ])
                ->add('region', CountryType::class, [
                    'label' => $this->translator->trans('region-country'),
                    'data' => $user->getCountry(),
                ])
                ->add('currency', CurrencyType::class, [
                    'choice_loader' => null,
                    'choices' => [
                        'EUR' => 'EUR',
                        'CZK' => 'CZK',
                        'USD' => 'USD',
                    ],
                    'label' => $this->translator->trans('currency'),
                    'data' => $user->getCurrency(),
                ])
                ->add('timezone', TimezoneType::class, [
                    'data' => $user->getTimezone(),
                ]);
        } else {
            $builder->add('locale', LanguageType::class, [
                'label' => $this->translator->trans('language'),
                'choice_loader' => null,
                'choices' => [
                    'English' => 'en',
                    'Čeština' => 'cz',
                    'Русский' => 'ru',
                ],
                'data' => $this->regionalSettingsService->getRegionalSettingValueObject()->getLocale(),
            ])
                ->add('region', CountryType::class, [
                    'alpha3' => true,
                    'label' => $this->translator->trans('region-country'),
                    'data' => $this->regionalSettingsService->getRegionalSettingValueObject()->getRegion(),
                ])
                ->add('currency', CurrencyType::class, [
                    'choice_loader' => null,
                    'choices' => [
                        'EUR' => 'EUR',
                        'CZK' => 'CZK',
                        'USD' => 'USD',
                    ],
                    'label' => $this->translator->trans('currency'),
                    'data' => $this->regionalSettingsService->getRegionalSettingValueObject()->getCurrency(),
                ])
                ->add('timezone', TimezoneType::class, [
                    'data' => $this->regionalSettingsService->getRegionalSettingValueObject()->getTimezone(),
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['request']);
        $resolver->setDefaults([
            'data_class' => RegionalSettingValueObject::class
        ]);
    }
}

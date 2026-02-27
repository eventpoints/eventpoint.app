<?php

declare(strict_types=1);

namespace App\Form\Filter;

use App\Autocomplete\Provider\CityProvider;
use App\DataTransferObject\EventFilterDto;
use App\Entity\City;
use App\Entity\Event\Category;
use App\Enum\EventFilterDateRangeEnum;
use App\Repository\CountryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventFilterType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly CountryRepository $countryRepository,
    ) {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $country = $this->countryRepository->findOneBy([
            'alpha2' => 'CZ',
        ]);

        $builder
            ->add('keyword', TextType::class, [
                'label' => $this->translator->trans('event-filter-title-placeholder'),
                'required' => false,
            ])
            ->add('period', EnumType::class, [
                'label' => false,
                'class' => EventFilterDateRangeEnum::class,
                'choice_label' => fn (EventFilterDateRangeEnum $enum) => $enum->trans($this->translator, $this->translator->getLocale()),
                'placeholder' => 'period',
                'autocomplete' => true,
                'theme' => 'flowbite',
            ])
            ->add('categories', EntityType::class, [
                'label' => false,
                'placeholder' => $this->translator->trans('categories'),
                'multiple' => true,
                'class' => Category::class,
                'choice_label' => 'title',
                'choice_value' => 'id',
                'autocomplete' => true,
                'translation_domain' => 'categories',
                'limit' => 30,
                'required' => false,
                'theme' => 'flowbite',
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'placeholder' => 'city',
                'data' => $country?->getCapitalCity(),
                'provider' => CityProvider::class,
                'choice_label' => fn (City $city): string => $this->translator->trans('city.' . strtolower((string) $city->getCountry()->getAlpha2()) . '.' . $city->getName(), domain: 'cities'),
                'extra_params' => [
                    'country' => $country->getId(),
                ],
                'autocomplete' => true,
                'theme' => 'flowbite',
            ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventFilterDto::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}

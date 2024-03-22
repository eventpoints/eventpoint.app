<?php

declare(strict_types=1);

namespace App\Form\Filter;

use App\DataTransferObject\EventFilterDto;
use App\Entity\City;
use App\Entity\Country;
use App\Entity\Event\Category;
use App\Enum\EventFilterDateRangeEnum;
use App\Form\Type\CustomEnumType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class EventFilterType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        $builder
            ->add('keyword', TextType::class, [
                'label' => $this->translator->trans('event-filter-title-placeholder'),
                'attr' => [
                ],
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('period', CustomEnumType::class, [
                'label' => false,
                'class' => EventFilterDateRangeEnum::class,
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('category', EntityType::class, [
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'class' => Category::class,
                'choice_label' => 'title',
                'translation_domain' => true,
                'autocomplete' => true,
                'multiple' => false,
            ])
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'name',
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->addDependent('city', 'country', function (DependentField $field, Country $country) {
                $field->add(EntityType::class, [
                    'class' => City::class,
                    'placeholder' => 'city',
                    'data' => $country->getCapitalCity(),
                    'choices' => $country->getCities(),
                    'choice_label' => fn (City $city): string => ucfirst($city->getName()),
                    'row_attr' => [
                        'class' => 'form-floating',
                    ],
                ]);
            })
            ->addDependent('radius', 'city', function (DependentField $field, null|City $city) {
                if ($city instanceof City) {
                    $field->add(RangeType::class, [
                        'label' => false,
                        'required' => false,
                        'data' => 50,
                        'attr' => [
                            'min' => 5,
                            'max' => 100,
                        ],
                    ]);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventFilterDto::class,
        ]);
    }
}

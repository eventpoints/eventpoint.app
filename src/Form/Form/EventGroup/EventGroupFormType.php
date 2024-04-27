<?php

declare(strict_types=1);

namespace App\Form\Form\EventGroup;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Event\Category;
use App\Entity\EventGroup\EventGroup;
use App\Entity\User\User;
use App\Form\Type\CustomCheckBoxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class EventGroupFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);
        $builder
            ->add('name', TextType::class, [
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('image', FileType::class, [
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => $this->translator->trans('select-valid-image-format', [
                            'formats' => implode(', ', ['JPEG', 'PNG', 'GIF']),
                        ]),
                    ]),
                ],
            ])
            ->add('language', LanguageType::class, [
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'autocomplete' => true,
            ])
            ->add('entityIdentificationNumber', TextType::class, [
                'help' => $this->translator->trans('entity-identification-number-explainer'),
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('purpose', TextType::class, [
                'label' => $this->translator->trans('group-purpose'),
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('isPrivate', CustomCheckBoxType::class, [
                'required' => false,
                'label' => $this->translator->trans('is-private-group-input-label'),
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'choice_translation_domain' => true,
                'multiple' => true,
                'autocomplete' => true,
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'name',
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->addDependent('city', 'country', function (DependentField $field, null|Country $country) {
                $field->add(EntityType::class, [
                    'class' => City::class,
                    'placeholder' => 'city',
                    'disabled' => ! $country instanceof Country,
                    'choices' => $country?->getCities(),
                    'choice_label' => fn (City $city): string => ucfirst($city->getName()),
                    'row_attr' => [
                        'class' => 'form-floating',
                    ],
                ]);
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventGroup::class,
            'owner' => User::class,
        ]);
    }
}

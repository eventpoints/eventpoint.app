<?php

declare(strict_types=1);

namespace App\Form\Form\EventGroup;

use App\Entity\Event\Category;
use App\Entity\EventGroup\EventGroup;
use App\Entity\User\User;
use App\Form\Type\CustomCheckBoxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventGroupSettingsFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'disabled' => true,
            ])
            ->add('image', FileType::class, [
                'required' => false,
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
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'autocomplete' => true,
            ])
            ->add('country', CountryType::class, [
                'required' => false,
                'help' => $this->translator->trans('leave-blank-if-not-applicable'),
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'autocomplete' => true,
            ])
            ->add('city', TextType::class, [
                'required' => false,
                'help' => $this->translator->trans('leave-blank-if-not-applicable'),
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('entityIdentificationNumber', TextType::class, [
                'required' => true,
                'help' => $this->translator->trans('entity-identification-number-explainer'),
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('purpose', TextType::class, [
                'required' => true,
                'label' => $this->translator->trans('group-purpose'),
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('isPrivate', CustomCheckBoxType::class, [
                'label' => $this->translator->trans('is-private-group-input-label'),
            ])
            ->add('categories', EntityType::class, [
                'label' => false,
                'multiple' => true,
                'class' => Category::class,
                'choice_label' => 'title',
                'choice_translation_domain' => true,
                'autocomplete' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventGroup::class,
            'owner' => User::class,
        ]);
    }
}

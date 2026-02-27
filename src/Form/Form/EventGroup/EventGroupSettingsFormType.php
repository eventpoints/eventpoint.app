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
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class EventGroupSettingsFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'disabled' => true,
            ])
            ->add('imageFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
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

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventGroup::class,
            'owner' => User::class,
        ]);
    }
}

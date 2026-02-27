<?php

declare(strict_types=1);

namespace App\Form\Form\User;

use App\Entity\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class UserAccountFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'row_attr' => [
                ],
            ])
            ->add('lastName', TextType::class, [
                'row_attr' => [
                ],
            ])
            ->add('locale', LanguageType::class, [
                'label' => $this->translator->trans('language'),
                'choice_loader' => null,
                'placeholder' => $this->translator->trans('language'),
                'choices' => [
                    'English' => 'en',
                    'Čeština' => 'cz',
                    'Русский' => 'ru',
                ],
            ])
            ->add('country', CountryType::class, [
                'label' => $this->translator->trans('region-country'),
                'placeholder' => $this->translator->trans('region-country'),
                'required' => false,
            ])
            ->add('currency', CurrencyType::class, [
                'label' => $this->translator->trans('currency'),
                'required' => false,
                'placeholder' => $this->translator->trans('currency'),

            ])
            ->add('timezone', TimezoneType::class, [
                'required' => false,
            ])
            ->add('avatarFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
            ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

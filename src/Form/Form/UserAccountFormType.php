<?php

declare(strict_types=1);

namespace App\Form\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserAccountFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

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
                'choices' => [
                    'English' => 'en',
                    'Čeština' => 'cz',
                    'Русский' => 'ru',
                ],
            ])
            ->add('country', CountryType::class, [
                'label' => $this->translator->trans('region-country', [
                    'required' => false,
                ]),
            ])
            ->add('currency', CurrencyType::class, [
                'label' => $this->translator->trans('currency', [
                    'required' => false,
                ]),
            ])
            ->add('timezone', TimezoneType::class, [
                'required' => false,
            ])
            ->add('avatar', FileType::class, [
                'row_attr' => [
                    'class' => 'w-75',
                ],
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

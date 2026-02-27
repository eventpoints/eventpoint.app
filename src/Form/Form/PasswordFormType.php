<?php

declare(strict_types=1);

namespace App\Form\Form;

use App\Form\Type\PasswordToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PasswordFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordToggleType::class,
            'mapped' => false,
            'invalid_message' => $this->translator->trans('password.must-match'),
            'first_options' => [
                'label' => $this->translator->trans('password.new-password'),
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => $this->translator->trans('password.new-password'),
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(min: 8),
                ],
            ],
            'second_options' => [
                'label' => $this->translator->trans('password.repeat-password'),
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => $this->translator->trans('password.repeat-password'),
                ],
            ],
        ]);
    }
}

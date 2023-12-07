<?php

declare(strict_types=1);

namespace App\Form\Form;

use App\Entity\Feedback;
use App\Enum\FeedbackEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeedbackFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EnumType::class, [
                'class' => FeedbackEnum::class,
                'label' => false,
            ])
            ->add('content', TextareaType::class, [
                'label' => $this->translator->trans('feedback'),
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'attr' => [
                    'placeholder' => $this->translator->trans('feedback'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
        ]);
    }
}

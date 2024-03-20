<?php

declare(strict_types=1);

namespace App\Form\Form\Event;

use App\Entity\Event\EventCancellation;
use App\Enum\EventCancellationReasonEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventCancellationFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason', EnumType::class, [
                'label' => $this->translator->trans('reason-for-cancellation'),
                'class' => EventCancellationReasonEnum::class,
            ])->add('notice', TextareaType::class, [
                'required' => false,
                'help' => $this->translator->trans('optional-form-help'),
                'attr' => [
                    'class' => 'vh-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventCancellation::class,
        ]);
    }
}

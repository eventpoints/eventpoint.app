<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Embeddable\Money;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoneyType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', NumberType::class, [
                'scale' => 2,
                'required' => false,
                'attr' => [
                    'step' => '0.01',
                    'min' => '0',
                ],
                'label' => $options['amount_label'],
            ])
            ->add('currency', CurrencyType::class, [
                'autocomplete' => true,
                'theme' => 'flowbite',
                'preferred_choices' => ['CZK', 'EUR', 'USD', 'GBP'],
                'label' => $options['currency_label'],
            ]);

        $builder->get('amount')->addModelTransformer(new CallbackTransformer(
            fn (?int $cents): ?float => $cents !== null ? $cents / 100 : null,
            fn (mixed $val): ?int => ($val !== null && $val !== '') ? (int) round((float) $val * 100) : null,
        ));
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Money::class,
            'amount_label' => 'Price',
            'currency_label' => 'Currency',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Form\Form\Event;

use App\Entity\Event\EventTicketOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventTicketOptionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])
            ->add('quantityAvailable', NumberType::class, [
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'required' => false,
            ])
            ->add('price', MoneyType::class, [
                'currency' => false,
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('currency', CurrencyType::class, [
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventTicketOption::class,
        ]);
    }
}

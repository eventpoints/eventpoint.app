<?php

declare(strict_types=1);

namespace App\Form\Form\Event;

use App\Entity\Event\EventTicketOption;
use App\Form\Type\MoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventTicketOptionFormType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('price', MoneyType::class, [
                'amount_label' => 'Price',
                'currency_label' => 'Currency',
            ])
            ->add('quantityAvailable', NumberType::class, [
                'required' => false,
            ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventTicketOption::class,
        ]);
    }
}

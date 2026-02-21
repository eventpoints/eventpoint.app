<?php

declare(strict_types=1);

namespace App\Form\Form\Ticketing;

use App\Entity\Event\EventTicketOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class CheckoutForm extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var EventTicketOption[] $ticketOptions */
        $ticketOptions = $options['ticket_options'];

        foreach ($ticketOptions as $ticketOption) {
            $fieldName = 'ticket_' . $ticketOption->getId()->toRfc4122();
            $max = $ticketOption->getMaxPerOrder() ?? 10;

            $builder->add($fieldName, IntegerType::class, [
                'label' => $ticketOption->getTitle(),
                'data' => 0,
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual(0),
                    new LessThanOrEqual($max),
                ],
                'attr' => [
                    'min' => 0,
                    'max' => $max,
                ],
            ]);
        }
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'ticket_options' => [],
        ]);
        $resolver->setAllowedTypes('ticket_options', 'array');
    }
}

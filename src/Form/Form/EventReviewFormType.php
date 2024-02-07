<?php

declare(strict_types=1);

namespace App\Form\Form;

use App\Entity\EventReview;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventReviewFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('venueRating', NumberType::class)
            ->add('contentRating', NumberType::class)
            ->add('hostRating', NumberType::class)
            ->add('guestRating', NumberType::class)
            ->add('expectationRating', NumberType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventReview::class,
        ]);
    }
}

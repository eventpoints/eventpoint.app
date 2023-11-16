<?php

declare(strict_types=1);

namespace App\Form\Form;

use App\Entity\Event\Event;
use App\Entity\EventGroup\EventGroup;
use App\Entity\User;
use App\Form\Type\EntitySelectionGroupType;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventGroupFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ])->add('events', EntitySelectionGroupType::class, [
                'class' => Event::class,
                'choice_label' => fn (Event $event): string => $event->getTitleAndDate(),
                'searchable' => false,
                'label' => 'add events',
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    $qb = $er->createQueryBuilder('event');

                    $qb->andWhere(
                        $qb->expr()->isNull('event.eventGroup')
                    );

                    $qb->orderBy('event.startAt', Criteria::DESC);
                    return $qb;
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventGroup::class,
            'owner' => User::class,
        ]);
    }
}

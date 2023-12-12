<?php

declare(strict_types=1);

namespace App\Form\Form;

use App\Entity\PhoneNumber;
use App\Entity\User;
use App\Form\Type\EntitySelectionGroupType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultPhoneNumberFormType extends AbstractType
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $this->security->getUser();
        if (! $currentUser instanceof User) {
            return;
        }

        $builder
            ->add('phoneNumber', EntitySelectionGroupType::class, [
                'searchable' => false,
                'data' => $currentUser->getPhoneNumber(),
                'expanded' => true,
                'multiple' => false,
                'query_builder' => function (EntityRepository $er) use ($currentUser): QueryBuilder {
                    $qb = $er->createQueryBuilder('phone_number');
                    $qb->andWhere(
                        $qb->expr()->eq('phone_number.owner', ':owner')
                    )->setParameter('owner', $currentUser->getId());
                    return $qb;
                },
                'class' => PhoneNumber::class,
                'choice_label' => fn (PhoneNumber $phoneNumber): string => $phoneNumber->getCode() . ' ' . $phoneNumber->getNumber(),
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}

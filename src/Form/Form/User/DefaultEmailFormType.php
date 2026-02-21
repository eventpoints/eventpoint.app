<?php

declare(strict_types=1);

namespace App\Form\Form\User;

use App\Entity\User\Email;
use App\Entity\User\User;
use App\Form\Type\EntitySelectionGroupType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultEmailFormType extends AbstractType
{
    public function __construct(
            private readonly Security $security
    )
    {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof User) {
            return;
        }

        $builder
                ->add('email', EntitySelectionGroupType::class, [
                        'label' => false,
                        'multiple' => false,
                        'searchable' => false,
                        'class' => Email::class,
                        'data' => $currentUser->getEmail(),
                        'query_builder' => function (EntityRepository $entityRepository) use ($currentUser): QueryBuilder {
                            $qb = $entityRepository->createQueryBuilder('email');
                            $qb->andWhere(
                                    $qb->expr()->eq('email.owner', ':owner')
                            )->setParameter('owner', $currentUser);
                            return $qb;
                        },
                        'choice_label' => fn(Email $email): string => $email->getAddress(),
                ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                'data_class' => null,
        ]);
    }
}

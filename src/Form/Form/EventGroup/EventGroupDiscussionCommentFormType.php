<?php

declare(strict_types=1);

namespace App\Form\Form\EventGroup;

use App\Entity\EventGroup\EventGroupDiscussionComment;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\TextEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventGroupDiscussionCommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextEditorType::class, [
                'attr' => [
                    'class' => 'vh-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventGroupDiscussionComment::class,
        ]);
    }
}

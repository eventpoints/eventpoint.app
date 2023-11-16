<?php

declare(strict_types=1);

namespace App\Form\Filter;

use App\DataTransferObject\EventFilterDto;
use App\Entity\Category;
use App\Form\Type\SelectionGroupType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventFilterType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod(Request::METHOD_GET)
            ->add('title', TextType::class, [
                'label' => $this->translator->trans('event-filter-title-placeholder'),
                'attr' => [
                    'placeholder' => $this->translator->trans('event-filter-title-placeholder'),
                ],
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('period', SelectionGroupType::class, [
                'label' => false,
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'choices' => [
                    'last-week' => 'last-week',
                    'today' => 'today',
                    'tomorrow' => 'tomorrow',
                    'this-week' => 'this-week',
                    'this-weekend' => 'this-weekend',
                    'next-week' => 'next-week',
                ],
                'searchable' => false,
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('category', EntityType::class, [
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'class' => Category::class,
                'choice_label' => 'title',
                'translation_domain' => true,
                'autocomplete' => true,
                'multiple' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventFilterDto::class,
        ]);
    }
}

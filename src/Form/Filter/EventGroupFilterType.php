<?php

declare(strict_types=1);

namespace App\Form\Filter;

use App\DataTransferObject\EventGroupFilterDto;
use App\Entity\Event\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventGroupFilterType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('keyword', TextType::class, [
                'label' => $this->translator->trans('groups-filter-placeholder'),
                'attr' => [
                ],
                'required' => false,
                'row_attr' => [
                    'class' => 'form-floating',
                ],
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
            'data_class' => EventGroupFilterDto::class,
        ]);
    }
}

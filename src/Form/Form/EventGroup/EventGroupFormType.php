<?php

declare(strict_types=1);

namespace App\Form\Form\EventGroup;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Event\Category;
use App\Entity\EventGroup\EventGroup;
use App\Entity\User\User;
use App\Form\Type\CustomCheckBoxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class EventGroupFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);
        $builder
            ->add('name', TextType::class, [
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('language', LanguageType::class, [
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'autocomplete' => true,
            ])
            ->add('description', TextType::class, [
                'label' => $this->translator->trans('group-purpose'),
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
                ->add('categories', EntityType::class, [
                        'label' => $this->translator->trans(id: 'categories', domain: 'messages'),
                        'attr' => [
                                'placeholder' => $this->translator->trans(id: 'event-categories-placeholder', domain: 'messages'),
                        ],
                        'multiple' => true,
                        'class' => Category::class,
                        'choice_label' => 'title',
                        'autocomplete' => true,
                        'translation_domain' => 'categories',
                        'limit' => 30,
                        'required' => false,
                        'theme' => 'flowbite',
                ])
        ;

    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventGroup::class,
            'owner' => User::class,
        ]);
    }
}

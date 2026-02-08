<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\DataTransformer\FlowbiteDateTimeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FlowbiteDateTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Date part (Flowbite)
        $builder->add('date', TextType::class, [
            'required' => $options['required'],
            'attr' => array_replace([
                'placeholder' => $options['placeholder'] ?: 'Select date',
                'datepicker' => 'true',
                'datepicker-format' => $options['flowbite_format'],
            ], $options['date_attr']),
        ]);

        // Time part
        $builder->add('time', TimeType::class, [
            'required' => $options['required'],
            'input' => 'string',
            'widget' => 'single_text',
            'with_seconds' => $options['with_seconds'],
            'attr' => array_replace([
                'min' => $options['time_min'] ?: null,
                'max' => $options['time_max'] ?: null,
                'step' => $options['time_step'] ?: null,
            ], $options['time_attr']),
        ]);

        $builder->addModelTransformer(new FlowbiteDateTimeTransformer(
            $options['php_date_format'],
            $options['php_time_format'],
            $options['model_timezone']
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'required' => false,

            // PHP formats for the model/transformer
            'php_date_format' => 'Y-m-d',
            'php_time_format' => 'H:i',
            'with_seconds' => false,

            // Flowbite datepicker format (NOT PHP format)
            'flowbite_format' => 'yyyy-mm-dd',

            // Optional timezone normalization (e.g., 'Europe/Prague'); null = unchanged
            'model_timezone' => null,

            // Extra attributes
            'date_attr' => [],
            'time_attr' => [],
            'time_min' => null,  // like "09:00"
            'time_max' => null,  // like "18:00"
            'time_step' => null,  // seconds (e.g., 60)

            // Presentation
            'label' => 'Date & time',
            'row_attr' => [
                'class' => 'space-y-2 max-w-sm',
            ],
            'placeholder' => 'Select date',
            'show_floating_label' => true,
        ]);
        $resolver->setAllowedTypes('show_floating_label', 'bool');
    }

    public function getBlockPrefix(): string
    {
        return 'flowbite_datetime';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['show_floating_label'] = $options['show_floating_label'];
    }
}
